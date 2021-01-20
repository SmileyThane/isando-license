<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Repositories\ConfigurationRepository;
// use Mint\Service\Repositories\InitRepository;

class ScriptMint
{
    protected $config;
    protected $repo;

    public function __construct(ConfigurationRepository $config) /* , InitRepository $repo */
    {
        $this->config = $config;
        // $this->repo = $repo;
    }

    /**
     * Used to set default configuration
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
       // $this->repo->init();

        $this->config->setDefault();
        
        return $next($request);
    }
}
