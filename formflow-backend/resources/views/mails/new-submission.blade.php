@extends('mails.mail-template')

@section('first-message')
    You have received new submission on your {{ $submission->form->name }}
@endsection

@section('content')
    <table style="font-family:arial,helvetica,sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
        <tbody>
        <tr>
            <td style="overflow-wrap:break-word;word-break:break-word;padding:30px 0px 15px;font-family:arial,helvetica,sans-serif;" align="left">
                <h1 style="margin: 0px; line-height: 140%; text-align: left; word-wrap: break-word; font-size: 16px; font-weight: 700; ">You have received a new submission on your form.</h1>
            </td>
        </tr>
        </tbody>
    </table>

    <table style="font-family:arial,helvetica,sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
        <tbody>
        <tr>
            <td style="overflow-wrap:break-word;word-break:break-word;padding:0px;font-family:arial,helvetica,sans-serif;" align="left">
                <div style="line-height: 140%; text-align: left; word-wrap: break-word;">
                    <p style="line-height: 140%;">Please find the submission details below:</p>
                </div>
            </td>
        </tr>
        </tbody>
    </table>

    <table style="font-family:arial,helvetica,sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
        <tbody>
        <tr>
            @if(count($submission->fields) === 0)
                <td style="overflow-wrap:break-word;word-break:break-word;padding:15px 0px 0px;font-family:arial,helvetica,sans-serif;font-size: 14px;line-height:1.5;color: #aaa;">We could not find any fields in this submission.<br>Please ensure that each input field in your form has a proper name.</td>
            @else
                <td style="overflow-wrap:break-word;word-break:break-word;padding:25px 0px 0px;font-family:arial,helvetica,sans-serif;" align="left">
                    <div>
                        <table style="width:100%;">
                            <tbody>
                            <tr style="border-top: 1px solid #eee;">
                                <td style="padding-top:10px;padding-bottom:10px;width:30%;padding-right:15px;font-size: 14px;"><strong>Project</strong></td>
                                <td style="padding-top:10px;padding-bottom:10px;font-size: 14px;"><strong>{{ $submission->form->project->name }}</strong></td>
                            </tr>
                            <tr style="border-top: 1px solid #eee;">
                                <td style="padding-top:10px;padding-bottom:10px;width:30%;padding-right:15px;font-size: 14px;"><strong>Form</strong></td>
                                <td style="padding-top:10px;padding-bottom:10px;font-size: 14px;color: {{ $submission->form->color->color }};">
                                    <div align="left" style="display: inline-block;vertical-align: middle;line-height: 16px;height: 16px;">
                                        <a href="{{ config('app.webapp') }}/forms/{{ $submission->form->hashId }}" target="_blank" class="v-button" style="box-sizing: border-box;display: inline-block;vertical-align:middle;font-family:arial,helvetica,sans-serif;text-decoration: none;-webkit-text-size-adjust: none;text-align: center;color: {{ ($submission->form->color->text === "light" ? "#FFFFFF" : "#111111")  }}; background-color: {{ $submission->form->color->color }}; border-radius: 5px;-webkit-border-radius: 5px; -moz-border-radius: 5px; max-width:100%; overflow-wrap: break-word; word-break: break-word; word-wrap:break-word; mso-border-alt: none;font-size: 14px;font-weight: 700; width: 16px; height: 16px;">&nbsp;</a>
                                    </div>
                                    <a href="{{ config('app.webapp') }}/forms/{{ $submission->form->hashId }}" style="display: inline-block;vertical-align:middle;line-height: 16px;color: {{ $submission->form->color->color }};text-decoration: none;font-weight: bold;margin-left:5px;">{{ $submission->form->name }}</a>
                                </td>
                            </tr>
                            @foreach($submission->fields as $key => $value)
                            <tr style="border-top: 1px solid #eee;">
                                <td style="padding-top:10px;padding-bottom:10px;width:30%;padding-right:15px;font-size: 14px;"><strong>{{ $key }}</strong></td>
                                <td style="padding-top:10px;padding-bottom:10px;font-size: 14px;">{{ $value }}</td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </td>
            @endif
        </tr>
        </tbody>
    </table>

    <table style="font-family:arial,helvetica,sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
        <tbody>
        <tr>
            <td style="overflow-wrap:break-word;word-break:break-word;padding:30px 0px 0px;font-family:arial,helvetica,sans-serif;" align="left">

                <div align="left">
                    <a href="/submissions/{{ $submission->hashId }}" target="_blank" class="v-button" style="box-sizing: border-box;display: inline-block;font-family:arial,helvetica,sans-serif;text-decoration: none;-webkit-text-size-adjust: none;text-align: center;color: #FFFFFF; background-color: #031feb; border-radius: 5px;-webkit-border-radius: 5px; -moz-border-radius: 5px; width:auto; max-width:100%; overflow-wrap: break-word; word-break: break-word; word-wrap:break-word; mso-border-alt: none;font-size: 14px;font-weight: 700; ">
                        <span style="display:block;padding:10px 30px;line-height:120%;"><span style="line-height: 16.8px;">View Submission</span></span>
                    </a>
                </div>

            </td>
        </tr>
        </tbody>
    </table>
@endsection
