<?php
namespace App\Repositories;

use App\Configuration;
use Illuminate\Validation\ValidationException;

class ConfigurationRepository
{
    protected $config;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    /**
     * Get all config variables
     * @return Configuration
     */
    public function getAll()
    {
        return $this->config->all()->pluck('value', 'name')->all();
    }

    /**
     * Get all config variables by public value
     * @return Configuration
     */
    public function getAllPublic()
    {
        return $this->config->all()->pluck('public_value', 'name')->all();
    }

    /**
     * Get config variable by name
     * @return Configuration
     */
    public function getByName($names)
    {
        return $this->config->filterByName($names)->first()->value;
    }

    /**
     * Get selected config variables by name
     * @return Configuration
     */
    public function getSelectedByName($names)
    {
        return $this->config->whereIn('name', $names)->get()->pluck('value', 'name')->all();
    }

    /**
     * Find configuration by name else create.
     *
     * @param array $params
     * @return null
     */
    public function firstOrCreate($name)
    {
        return $this->config->firstOrCreate(['name' => $name]);
    }

    /**
     * Store a configuration
     *
     * @param array $params
     * @return null
     */
    public function set($name, $value, $private = 0)
    {
        $config = $this->firstOrCreate([
            'name' => $name
        ]);

        $config->text_value = ($value) ? !is_numeric($value) ? $value : null : null;
        $config->numeric_value = is_numeric($value) ? $value : null;
        $config->is_private = $private;
        $config->save();

        return $config;
    }

    /**
     * Store configuration.
     *
     * @param array $params
     * @return null
     */
    public function create($params)
    {
        $this->testModeOperation($params);

        $this->smsConfiguration($params);

        foreach ($params as $key => $value) {
            if (! in_array($key, ['config_type']) && (!in_array($key, config('system.private_config_variables')) || (in_array($key, config('system.private_config_variables')) && $value != config('system.hidden_field')))) {
                $value = (is_array($value)) ? implode(',', $value) : $value;

                if (($key == 'cpanel_password' || $key == 'plesk_password') && $value) {
                    $value = encrypt($value);
                }

                $config = $this->firstOrCreate($key);
                $config->numeric_value = is_numeric($value) ? $value : null;
                $config->text_value = !is_numeric($value) ? $value : null;
                $config->save();
            }
        }

        $this->setLocale($params);

        $this->setVisibility();
    }

    /**
     * Store test mode configuration.
     *
     * @param array $params
     * @return null
     */
    public function testModeOperation($params)
    {
        $config_type = isset($params['config_type']) ? $params['config_type'] : null;

        if ($config_type != 'system') {
            return;
        }

        $mode = isset($params['mode']) ? $params['mode'] : 1;

        if (isTestMode() && $mode != $this->firstOrCreate('mode')) {
            $this->set('mode', $mode, 0);
            config(['config.mode' => $mode]);
        }

        if (isTestMode()) {
            throw ValidationException::withMessages(['message' => trans('general.restricted_test_mode_action')]);
        }
    }

    /**
     * Store SMS configuration.
     *
     * @param array $params
     * @return null
     */
    public function smsConfiguration($params)
    {
        $config_type = isset($params['config_type']) ? $params['config_type'] : null;
        $nexmo_api_key = isset($params['nexmo_api_key']) ? $params['nexmo_api_key'] : null;
        $nexmo_api_secret = isset($params['nexmo_api_secret']) ? $params['nexmo_api_secret'] : null;
        $nexmo_receiver_mobile = isset($params['nexmo_receiver_mobile']) ? $params['nexmo_receiver_mobile'] : null;
        $nexmo_sender_mobile = isset($params['nexmo_sender_mobile']) ? $params['nexmo_sender_mobile'] : null;

        if ($config_type != 'sms') {
            return;
        }

        config(['nexmo.api_key' => $nexmo_api_key,'nexmo.api_secret' => $nexmo_api_secret]);
        try {
            $nexmo = app('Nexmo\Client');
            $nexmo->message()->send([
                'to'   => $nexmo_receiver_mobile,
                'from' => $nexmo_sender_mobile,
                'text' => 'Test Message!'
            ]);
        } catch (\Nexmo\Client\Exception\Request $e) {
            throw ValidationException::withMessages(['nexmo_api_key' => [$e->getMessage()]]);
        }
    }

    /**
     * Store locale configuration.
     *
     * @param array $params
     * @return null
     */
    public function setLocale($params)
    {
        $config_type = isset($params['config_type']) ? $params['config_type'] : null;
        $locale = isset($params['locale']) ? $params['locale'] : config('app.locale');

        if ($config_type != 'system') {
            return;
        }

        if ($locale === config('app.locale')) {
            return;
        }

        config(['app.locale' => $locale]);
        \App::setLocale(config('app.locale'));
        \Cache::forget('lang.js');
    }

    /**
     * Set configuration visibility.
     *
     * @param array $params
     * @return null
     */
    public function setVisibility()
    {
        $this->config->whereIn('name', config('system.private_config_variables'))->update(['is_private' => 1]);
        $this->config->whereNotIn('name', config('system.private_config_variables'))->update(['is_private' => 0]);
    }

    /**
     * Set default configuration variable.
     *
     * @return null
     */
    public function setDefault()
    {
        $system_variables = getVar('system');
        config(['system' => $system_variables]);
        $default_config = isset($system_variables['default_config']) ? $system_variables['default_config'] : [];
        foreach($default_config as $key => $value)
            config(['config.'.$key => $value]);

        // Check for installation
        config(['config.failed_install' => 0]);

        if(!\Storage::exists('.app_installed')){
            config(['config.failed_install' => 1]);
            return false;
        }

        foreach ($default_config as $key => $value) {
            $config = $this->firstOrCreate($key);

            if (! is_numeric($config->numeric_value) && ($config->value === '' || $config->value === null)) {
                $config->numeric_value = is_numeric($value) ? $value : null;
                $config->text_value    = !is_numeric($value) ? $value : null;
                $config->save();
            }
        }

        config(['config' => $this->getAll()]);
        config(['system' => $system_variables]);
        config(['config.l' => (\Storage::exists('.access_code') ? 1 : 0)]);

        $this->setVisibility();

        config(['nexmo.api_key' => config('config.nexmo_api_key'),'nexmo.api_secret' => config('config.nexmo_api_secret')]);
        config(['jwt.ttl' => config('config.token_lifetime') ? : 120]);
        date_default_timezone_set(config('config.timezone') ? : 'Asia/Kolkata');
        \App::setLocale(config('config.locale') ? : 'en');
        
        config(['app.debug' => (!\App::environment('production') && config('config.error_display')) ? true : false]);

        config([
            'mail.driver'       => config('config.driver'),
            'mail.from.name'    => config('config.from_name'),
            'mail.from.address' => config('config.from_address')
        ]);

        if (config('mail.driver') === 'smtp') {
            config([
                'mail.host'       => config('config.smtp_host'),
                'mail.port'       => config('config.smtp_port'),
                'mail.encryption' => config('config.smtp_encryption'),
                'mail.username'   => config('config.smtp_username'),
                'mail.password'   => config('config.smtp_password')
            ]);
        } elseif (config('mail.driver') === 'mailgun') {
            config([
                'mail.host'               => config('config.mailgun_host'),
                'mail.port'               => config('config.mailgun_port'),
                'mail.encryption'         => config('config.mailgun_encryption'),
                'mail.username'           => config('config.mailgun_username'),
                'mail.password'           => config('config.mailgun_password'),
                'services.mailgun.domain' => config('config.mailgun_domain'),
                'services.mailgun.secret' => config('config.mailgun_secret')
            ]);
        }
    }

    /**
     * Get company address
     * @return string
     */
    public function getCompanyAddress()
    {
        $address = config('config.address_line_1');
        $address .= (config('config.address_line_2')) ? (', <br >'.config('config.address_line_2')) : '';
        $address .= (config('config.city')) ? ', <br >'.(config('config.city')) : '';
        $address .= (config('config.state')) ? ', '.(config('config.state')) : '';
        $address .= (config('config.zipcode')) ? ', '.(config('config.zipcode')) : '';
        $address .= (config('config.country_id')) ? '<br >'.(config('config.country')) : '';

        return $address;
    }

    /**
     * Get company logo
     * @return string
     */
    public function getCompanyLogo()
    {
        if (config('config.main_logo') && \File::exists(config('config.main_logo'))) {
            return '<img src="'.url('/'.config('config.main_logo')).'">';
        } else {
            return '<img src="'.url('/images/default_main_logo.png').'">';
        }
    }
}
