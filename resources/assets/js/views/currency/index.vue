<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('currency.currency')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('currency.currency')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-4 col-md-4" v-if="hasPermission('create-currency')">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('currency.add_new_currency')}}</h4>
                        <currency-form @completed="getCurrencies"></currency-form>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-8 col-md-8">
                <transition name="fade">
                    <div class="card" v-if="showFilterPanel">
                        <div class="card-body">
                                <button class="btn btn-info btn-sm pull-right" v-if="showFilterPanel" @click="showFilterPanel = !showFilterPanel">{{trans('general.hide')}}</button>
                            <h4 class="card-title">{{trans('general.filter')}}</h4>
                            <div class="row">
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('currency.name')}}</label>
                                        <input class="form-control" name="name" v-model="filterCurrencyForm.name">
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('general.sort_by')}}</label>
                                        <select name="order" class="form-control" v-model="filterCurrencyForm.sort_by">
                                            <option value="name">{{trans('currency.name')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('general.order')}}</label>
                                        <select name="order" class="form-control" v-model="filterCurrencyForm.order">
                                            <option value="asc">{{trans('general.ascending')}}</option>
                                            <option value="desc">{{trans('general.descending')}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </transition>
                <div class="card">
                    <div class="card-body">
                        <button class="btn btn-info btn-sm pull-right" v-if="!showFilterPanel" @click="showFilterPanel = !showFilterPanel"><i class="fas fa-filter"></i> {{trans('general.filter')}}</button>
                        <h4 class="card-title">{{trans('currency.currency_list')}}</h4>
                        <h6 class="card-subtitle" v-if="currencies">{{trans('general.total_result_found',{'count' : currencies.total})}}</h6>
                        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
                        <div class="table-responsive" v-if="hasPermission('list-currency') && currencies.total">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{trans('currency.name')}}</th>
                                        <th>{{trans('currency.symbol')}}</th>
                                        <th>{{trans('currency.position')}}</th>
                                        <th>{{trans('currency.decimal_place')}}</th>
                                        <th>{{trans('currency.default')}}</th>
                                        <th class="table-option">{{trans('general.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="currency in currencies.data">
                                        <td v-text="currency.name"></td>
                                        <td v-text="currency.symbol"></td>
                                        <td>{{trans('currency.'+currency.position)}}</td>
                                        <td v-text="currency.decimal_place"></td>
                                        <td v-html="getDefaultStatus(currency)"></td>
                                        <td class="table-option">
                                            <div class="btn-group">
                                                <button class="btn btn-info btn-sm" v-if="hasPermission('edit-currency')" v-tooltip="trans('currency.edit_currency')" @click.prevent="editCurrency(currency)"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-danger btn-sm" v-if="hasPermission('delete-currency')" :key="currency.id" v-confirm="{ok: confirmDelete(currency)}" v-tooltip="trans('currency.delete_currency')"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <module-info v-if="!currencies.total" module="currency" title="module_info_title" description="module_info_description" icon="money-bill-alt"></module-info>
                        <pagination-record :page-length.sync="filterCurrencyForm.page_length" :records="currencies" @updateRecords="getCurrencies" @change.native="getCurrencies"></pagination-record>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import currencyForm from './form'

    export default {
        components : { currencyForm },
        data() {
            return {
                currencies: {
                    total: 0,
                    data: []
                },
                filterCurrencyForm: {
                    name: '',
                    sort_by : 'name',
                    order: 'asc',
                    page_length: helper.getConfig('page_length')
                },
                showFilterPanel: false
            };
        },
        mounted(){
            this.getCurrencies();
        },
        methods: {
            hasPermission(permission){
                return helper.hasPermission(permission);
            },
            getCurrencies(page){
                if (typeof page !== 'number') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterCurrencyForm);
                axios.get('/api/currency?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.currencies = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            editCurrency(currency){
                this.$router.push('/currency/'+currency.id+'/edit');
            },
            confirmDelete(currency){
                return dialog => this.deleteCurrency(currency);
            },
            deleteCurrency(currency){
                axios.delete('/api/currency/'+currency.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getCurrencies();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            getConfig(config){
                return helper.getConfig(config);
            },
            getDefaultStatus(currency){
                return (currency.is_default) ? '<span class="label label-success">'+i18n.currency.default+'</span>' : '-';
            }
        },
        watch: {
            filterCurrencyForm: {
                handler(val){
                    this.getCurrencies();
                },
                deep: true
            }
        }
    }
</script>
