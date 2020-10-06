@extends('layouts.pdf')
@section('document')
    <style>
        h1, h2, h3, h4, p, td {
            font-family: "Times New Roman", Times, serif;
            color: black;
        }

        body {
            font-family: "Times New Roman", Times, serif;
        }

        p, td {
            font-size: 15pt;
        }
    </style>
    @php
        $i = 0;
    @endphp
    @foreach($items as $item)
        @php
        $i++
        @endphp
        <div class="col col-lg-8 offset-lg-2">
            <div class="col col-lgs-12" style="text-align: right;">
                <p>Утверждаю</p>
                <p>Директор ГБПОУ ИО "ИАТ"</p>
                <p>_______________Якубовский А.Н.</p>
            </div>
            <div class="col-lg-12" style="text-align: center; font-weight: bold;">
                <h3>ЗАКЛЮЧЕНИЕ О ТЕХНИЧЕСКОМ СОСТОЯНИИ ОСНОВНОГО СРЕДСТВА</h3>
            </div>
            <div class="col col-lg-12">
                <p><span style="font-weight: bold">Основное средство (наименование): </span> {{$item[0]->name}}</p>
                <p><span style="font-weight: bold">Инвентарный номер: </span><span
                            style="display: inline;">{{$item[0]->inv_number}}</span>
                </p>
                <p><span style="font-weight: bold">Техническое состояние на момент составления заключения: </span></p>
                <p style="display: inline;">{{$item['condition']}}</p>
                <p><span style="font-weight: bold">Заключение: </span></p>
                <p style="display: inline; width: 100%;">{{$item['conclusion']}}</p>
                <p style="font-weight: bold;">Комиссия:</p>

                <table class="col-lg-12" style="width: 100%;">
                    <tr>
                        <td style="white-space: nowrap; width: 35%;">Главный бухгалтер</td>
                        <td style="border-bottom: black solid 2px; width: 30%"></td>
                        <td style="white-space: nowrap; text-align: right; width: 35%;" class="text-right">Волошенко
                            Г.М
                        </td>
                    </tr>
                </table>
                <table class="col col-lg-12" style="width: 100%;">
                    <tr>
                        <td style="white-space: nowrap; width: 35%;">Зам. директора по ИТ</td>
                        <td style="border-bottom: black solid 2px; width: 30%"></td>
                        <td style="white-space: nowrap; width: 35%; text-align: right;">Чернигов П.Н.</td>
                    </tr>
                </table>
                <table class="col col-lg-12" style="width: 100%;">
                    <tr>
                        <td style="white-space: nowrap; width: 35%;">Преподаватель</td>
                        <td style="border-bottom: black solid 2px; width: 30%"></td>
                        <td style=" white-space: nowrap; text-align: right; width: 35%;">Михайлов С.А.</td>
                    </tr>
                </table>
                <table class="col col-lg-12" style="width: 100%;">
                    <tr>
                        <td style="white-space: nowrap; width: 35%;">Бухгалтер</td>
                        <td style="border-bottom: black solid 2px; width: 30%"></td>
                        <td style=" white-space: nowrap; text-align: right; width: 35%;">Шабельникова В.И.</td>
                    </tr>
                </table>
                <table class="col col-lg-12" style="width: 100%;">
                    <tr>
                        <td style="white-space: nowrap; width: 35%;">МОЛ</td>
                        <td style="border-bottom: black solid 2px; width: 30%;"></td>
                        <td style="white-space: nowrap; text-align: right; width: 35%;">Зайкова Е.П.</td>
                    </tr>
                </table>
            </div>
        </div>
        @if($i !== count($items))
            <div style="page-break-after: always;"></div>
        @endif
    @endforeach
@endsection