<!DOCTYPE html>
<html>
<head>
    <title>{{env('APP_NAME')}}</title>
    <meta charset="utf-8" />
    <style>
        *{font-family:'Helvetica';font-size: 14px;text-align: justify;}
    body{width:auto; max-width:800px;margin:0 auto;,font-size:14px;}
    h2{font-size: 16px;font-weight: bold;}
    table.table-head th{font-size: 14px; font-weight: bold;text-align: right;}
    table.table-head td{font-size: 14px;text-align: right;}

    table.fancy-detail {  font-size:14px; border-collapse: collapse;  width:100%;  margin:0 auto;}
    table.fancy-detail th{  background:#F5F5F5; border-bottom: 1px #2e2e2e solid;  padding: 0.5em;  padding-left:10px; vertical-align:top;text-align: left;}
    table.fancy-detail th, table.fancy-detail td  {  padding: 0.5em;  padding-left:10px; border-bottom:1px solid #2e2e2e;text-align: left;}
    table.fancy-detail caption {  margin-left: inherit;  margin-right: inherit;}
    table.fancy-detail tr:hover{}

    </style>
</head>
<body>
    <div style="background-color: #F5F5F5; padding:5px;">
        <div style="padding:10px;background: #ffffff;">
            <table border="0" style="width:100%;margin-top: 20px;height: 100px;">
            <tr>
                <td style="width:40%;vertical-align: top;">
                    <img src="{{url('/images/logo.png')}}" />
                </td>
                <td style="width:60%;vertical-align: top;">
                    <table align="right" class="table-head">
                        <tr>
                            <th>{{trans('subscription.number')}}</th><td>{{$subscription->subscription_number}}</td>
                        </tr>
                        <tr>
                            <th>{{trans('subscription.type')}}</th><td>{{$subscription->type === 'extend' ? 'Validity Extend' : 'Upgrade'}}</td>
                        </tr>
                        <tr>
                            <th>{{trans('subscription.date')}}</th><td>{{showDate($subscription->created_at)}}</td>
                        </tr>
                        <tr>
                            <th>{{trans('subscription.time')}}</th><td>{{showTime($subscription->created_at)}}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            </table>
            <table border="0" style="width:100%;">
                <tr>
                    <td colspan="3"><hr></td>
                </tr>
                <tr>
                    <td style="width:40%; vertical-align: top;">
                        <p style="font-size:14px;"><span style="font-size:16px; font-weight: bold;">{{config('config.company_name')}}</span><br />
                        {!! (config('config.email')) ? (trans('configuration.email').' : '.config('config.email').'<br />') : ''!!}
                        {!! (config('config.phone')) ? (trans('configuration.phone').' : '.config('config.phone').'<br />') : ''!!}
                        {!! config('config.address_line_1') ? (config('config.address_line_1').'<br />') : ''!!}
                        {!! config('config.address_line_2') ? (config('config.address_line_2').'<br />') : ''!!}
                        {!! config('config.city') ? (config('config.city').'<br />') : ''!!}
                        {!! config('config.state') ? (config('config.state').'<br />') : ''!!}
                        {!! config('config.zipcode') ? (config('config.zipcode').'<br />') : ''!!}
                        {!! config('config.country') ?  config('config.country') : ''!!}
                        </p>
                    </td>
                    <td style="width:20%;">
                        <img src="{{url('/images/paid.png')}}" />
                    </td>
                    <td style="width:40%; vertical-align: top;">
                        <p style="font-size:14px;text-align:right;"><span style="font-size:16px; font-weight: bold;">{{$subscription->Instance->name}}</span><br />
                        {!! trans('user.email').' : '.$subscription->Instance->email.'<br />'!!}
                        {!! ($subscription->phone) ? (trans('user.phone').' : '.$subscription->phone.'<br />') : ''!!}
                        {!! ($subscription->address_line_1) ? ($subscription->address_line_1.'<br />') : ''!!}
                        {!! ($subscription->address_line_2) ? ($subscription->address_line_2.'<br />') : ''!!}
                        {!! ($subscription->city) ? ($subscription->city.'<br />') : ''!!}
                        {!! ($subscription->state) ? ($subscription->state.'<br />') : ''!!}
                        {!! ($subscription->zipcode) ? ($subscription->zipcode.'<br />') : ''!!}
                        {!! ($subscription->country) ? getVar('country')[$subscription->country] : ''!!}
                        </p>
                    </td>
                </tr>
            </table>
            <div style="margin:5px 0px;">&nbsp;</div>
            <table class="fancy-detail">
                <thead>
                    <tr>
                        <th> # </th>
                        <th> {{trans('plan.plan')}} </th>
                        <th>{{trans('subscription.method')}}</th>
                        <th>{{trans('subscription.transaction_id')}}</th>
                        <th style="text-align: right;"> {{trans('subscription.validity')}} </th>
                        <th style="text-align: right;"> {{trans('subscription.amount')}} </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td> 1. </td>
                        <td> {{$subscription->Plan->name}} </td>
                        <td>{{$subscription->source === 'paypal' ? 'Paypal' : 'Credit Card'}}</td>
                        <td>{{$subscription->token}}</td>
                        <td style="text-align: right;"> {{showDate($subscription->validity)}} </td>
                        <td style="text-align: right;"> {{currency($subscription->amount,$subscription->Currency,1)}} </td>
                    </tr>
                </tbody>
            </table>
            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                <tr>
                    <td style="width:60%;vertical-align: top;">
                        <h2>{{trans('subscription.tnc')}}</h2>
                        <p style="font-size: 13px;">{!!config('config.subscription_invoice_tnc')!!}</p>
                    </td>
                    <td style="width:40%;vertical-align: top;">
                        <table class="fancy-detail" style="width:100%;">
                            <tr>
                                <td style="font-size:16px; font-weight: bold;">{{trans('subscription.total')}}</td>
                                <td style="width:150px; text-align: right;font-size:16px; font-weight: bold;">{{currency($subscription->amount,$subscription->Currency,1)}}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>

