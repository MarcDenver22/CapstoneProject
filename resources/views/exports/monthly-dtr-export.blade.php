<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 2.54cm; size: 21cm 29.7cm; }
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #000; font-size: 10px; margin: 0; padding: 0; line-height: 1.2; }

        /* two column layout - A4 portrait */
        .two-col { display: table; width: 100%; table-layout: fixed; margin: 0 auto; page-break-inside: avoid; }
        .col { display: table-cell; width: 50%; vertical-align: top; padding: 1px 0.5px; text-align: center; }

        .form-block { padding: 1px 0.5px; margin: 0 auto; width: 90%; }

        .center { text-align: center; margin: 0; }
        .small { font-size: 8px; }
        .title { font-size: 11px; font-weight: 700; letter-spacing: 0; margin: 0; }
        .header { font-size: 8px; margin: 0; }

        /* underlines */
        .u-line {
            display: inline-block;
            border-bottom: 1px solid #000;
            vertical-align: baseline;
            font-size: 8px;
            padding: 0 2px 1px 2px;
        }
        .u-name { min-width: 70px; }
        .u-month { min-width: 60px; }
        .u-hours { min-width: 50px; }
        .u-days { min-width: 25px; }

        /* table */
        table.form { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 1px auto 0; 
            table-layout: fixed; 
            font-size: 8px;
        }
        table.form th, table.form td {
            border: 1px solid #000;
            padding: 1px 0.5px;
            text-align: center;
            line-height: 1.2;
            overflow: hidden;
            word-wrap: break-word;
            height: 16px;
        }
        table.form th { font-weight: 700; font-size: 8px; }

        .w-day { width: 8%; }
        .w-time { width: 15%; }
        .w-und { width: 10%; }

        .total-row td { font-weight: 700; }

        .cert { font-size: 7px; margin: 0.5px 0 0 0; text-align: center; line-height: 1.2; padding: 0; }
        .sigline { border-bottom: 1px solid #000; width: 50%; height: 0; margin: 2px auto 0; line-height: 1; }
        .verified { margin-top: 3px; font-size: 7px; text-align: center; line-height: 1; }
        .sig-label { font-size: 7px; text-align: center; margin-top: 1px; line-height: 1; }

        .page-break { page-break-after: always; }
        .employee-section { page-break-after: always; }
    </style>
</head>
<body data-redirect-url="{{ $redirect_route ?? route('hr.reports.index') }}">
    @forelse($dtrExports as $exportIndex => $export)
    <div class="employee-section">
        <div class="two-col">
            <div class="col">
                <div class="form-block">
                    <div class="center header">Civil Service Form No. 48</div>
                    <div class="center title" style="margin-bottom:1.5px;">DAILY TIME RECORD</div>
                    <div class="center" style="font-size:10px; margin:0 0 2.5px 0;">-------o0o-------</div>

                    <div class="center" style="margin:2.5px 0 1.5px 0;">
                        <span class="u-line u-name">{{ substr($export['user']->name ?? '', 0, 20) }}</span><br>
                        <span class="small">(Name)</span>
                    </div>

                    <div style="margin-top:2.5px; font-size:9px; text-align: left; line-height: 1.6; width:92%; margin-left:auto; margin-right:auto;">
                        <div style="margin-bottom:2px;">For the month of <span class="u-line u-month" style="min-width:100px;">{{ \Carbon\Carbon::createFromFormat('m-Y', $export['month'] . '-' . $export['year'])->format('M Y') }}</span></div>
                        <table style="width:100%; border-collapse: collapse;">
                            <tr>
                                <td style="width:50%; vertical-align: top; padding: 0;">
                                    <div>Official hours for</div>
                                    <div>arrival and departure</div>
                                </td>
                                <td style="width:50%; text-align: right; padding: 0; vertical-align: top;">
                                    <div style="margin-bottom:2px;">Regular days <span class="u-line u-days" style="min-width:50px;"></span></div>
                                    <div>Saturdays <span class="u-line u-days" style="min-width:50px;"></span></div>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <table class="form" style="margin-top:3px;">
                        <thead>
                        <tr>
                            <th rowspan="2" class="w-day">Day</th>
                            <th colspan="2">A.M.</th>
                            <th colspan="2">P.M.</th>
                            <th colspan="2">Undertime</th>
                        </tr>
                        <tr>
                            <th class="w-time">Arrival</th>
                            <th class="w-time">Departure</th>
                            <th class="w-time">Arrival</th>
                            <th class="w-time">Departure</th>
                            <th class="w-und">Hrs</th>
                            <th class="w-und">Min</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $totalHours = 0;
                            $totalMinutes = 0;
                        @endphp
                        @for ($day = 1; $day <= 31; $day++)
                            @php
                                $record = isset($export['daysData'][$day]) ? (object)$export['daysData'][$day] : null;
                                $amIn = $record?->am_arrival ?? '';
                                $amOut = $record?->am_depart ?? '';
                                $pmIn = $record?->pm_arrival ?? '';
                                $pmOut = $record?->pm_depart ?? '';
                                $utHours = $record?->undertime_hours ?? '';
                                $utMinutes = $record?->undertime_minutes ?? '';

                                if ($utHours !== '' && $utMinutes !== '') {
                                    $totalHours += intval($utHours);
                                    $totalMinutes += intval($utMinutes);
                                    if ($totalMinutes >= 60) {
                                        $totalHours += intdiv($totalMinutes, 60);
                                        $totalMinutes = $totalMinutes % 60;
                                    }
                                }
                            @endphp
                            <tr>
                                <td>{{ $day }}</td>
                                <td>{{ $amIn }}</td>
                                <td>{{ $amOut }}</td>
                                <td>{{ $pmIn }}</td>
                                <td>{{ $pmOut }}</td>
                                <td>{{ $utHours }}</td>
                                <td>{{ $utMinutes }}</td>
                            </tr>
                        @endfor

                        <tr class="total-row">
                            <td colspan="5">Total</td>
                            <td>{{ $totalHours }}</td>
                            <td>{{ $totalMinutes }}</td>
                        </tr>
                        </tbody>
                    </table>

                    <div class="cert" style="margin-top:2.5px; font-size:8px; line-height: 1.4; margin-bottom:2px; text-align: left; width:92%; margin-left:auto; margin-right:auto;">
                        I certify on my honor that the above is a true and correct report of the hours of work performed, record of which was made daily at the time of arrival and departure from office.
                    </div>

                    <div class="sigline" style="margin-top:8px; margin-bottom:2.5px; border-bottom: 2px solid #000; width:92%; margin-left:auto; margin-right:auto;"></div>
                    <div class="verified" style="text-align: center;">VERIFIED as to the prescribed office hours:</div>
                    <div class="sigline" style="margin-top:8px; margin-bottom:1px; border-bottom: 2px solid #000; width:92%; margin-left:auto; margin-right:auto;"></div>
                    <div class="sig-label" style="margin-top:1px; font-weight: 700; text-align: center;">In Charge</div>
                </div>
            </div>

            <div class="col">
                <div class="form-block">
                    <div class="center header">Civil Service Form No. 48</div>
                    <div class="center title" style="margin-bottom:1.5px;">DAILY TIME RECORD</div>
                    <div class="center" style="font-size:10px; margin:0 0 2.5px 0;">-------o0o-------</div>

                    <div class="center" style="margin:2.5px 0 1.5px 0;">
                        <span class="u-line u-name">{{ substr($export['user']->name ?? '', 0, 20) }}</span><br>
                        <span class="small">(Name)</span>
                    </div>

                    <div style="margin-top:2.5px; font-size:9px; text-align: left; line-height: 1.6; width:92%; margin-left:auto; margin-right:auto;">
                        <div style="margin-bottom:2px;">For the month of <span class="u-line u-month" style="min-width:100px;">{{ \Carbon\Carbon::createFromFormat('m-Y', $export['month'] . '-' . $export['year'])->format('M Y') }}</span></div>
                        <table style="width:100%; border-collapse: collapse;">
                            <tr>
                                <td style="width:50%; vertical-align: top; padding: 0;">
                                    <div>Official hours for</div>
                                    <div>arrival and departure</div>
                                </td>
                                <td style="width:50%; text-align: right; padding: 0; vertical-align: top;">
                                    <div style="margin-bottom:2px;">Regular days <span class="u-line u-days" style="min-width:50px;"></span></div>
                                    <div>Saturdays <span class="u-line u-days" style="min-width:50px;"></span></div>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <table class="form" style="margin-top:3px;">
                        <thead>
                        <tr>
                            <th rowspan="2" class="w-day">Day</th>
                            <th colspan="2">A.M.</th>
                            <th colspan="2">P.M.</th>
                            <th colspan="2">Undertime</th>
                        </tr>
                        <tr>
                            <th class="w-time">Arrival</th>
                            <th class="w-time">Departure</th>
                            <th class="w-time">Arrival</th>
                            <th class="w-time">Departure</th>
                            <th class="w-und">Hrs</th>
                            <th class="w-und">Min</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $totalHours = 0;
                            $totalMinutes = 0;
                        @endphp
                        @for ($day = 1; $day <= 31; $day++)
                            @php
                                $record = isset($export['daysData'][$day]) ? (object)$export['daysData'][$day] : null;
                                $amIn = $record?->am_arrival ?? '';
                                $amOut = $record?->am_depart ?? '';
                                $pmIn = $record?->pm_arrival ?? '';
                                $pmOut = $record?->pm_depart ?? '';
                                $utHours = $record?->undertime_hours ?? '';
                                $utMinutes = $record?->undertime_minutes ?? '';

                                if ($utHours !== '' && $utMinutes !== '') {
                                    $totalHours += intval($utHours);
                                    $totalMinutes += intval($utMinutes);
                                    if ($totalMinutes >= 60) {
                                        $totalHours += intdiv($totalMinutes, 60);
                                        $totalMinutes = $totalMinutes % 60;
                                    }
                                }
                            @endphp
                            <tr>
                                <td>{{ $day }}</td>
                                <td>{{ $amIn }}</td>
                                <td>{{ $amOut }}</td>
                                <td>{{ $pmIn }}</td>
                                <td>{{ $pmOut }}</td>
                                <td>{{ $utHours }}</td>
                                <td>{{ $utMinutes }}</td>
                            </tr>
                        @endfor

                        <tr class="total-row">
                            <td colspan="5">Total</td>
                            <td>{{ $totalHours }}</td>
                            <td>{{ $totalMinutes }}</td>
                        </tr>
                        </tbody>
                    </table>

                    <div class="cert" style="margin-top:2.5px; font-size:8px; line-height: 1.4; margin-bottom:2px; text-align: left; width:92%; margin-left:auto; margin-right:auto;">
                        I certify on my honor that the above is a true and correct report of the hours of work performed, record of which was made daily at the time of arrival and departure from office.
                    </div>

                    <div class="sigline" style="margin-top:8px; margin-bottom:2.5px; border-bottom: 2px solid #000; width:92%; margin-left:auto; margin-right:auto;"></div>
                    <div class="verified" style="text-align: center;">VERIFIED as to the prescribed office hours:</div>
                    <div class="sigline" style="margin-top:8px; margin-bottom:1px; border-bottom: 2px solid #000; width:92%; margin-left:auto; margin-right:auto;"></div>
                    <div class="sig-label" style="margin-top:1px; font-weight: 700; text-align: center;">In Charge</div>
                </div>
            </div>
        </div>
    </div>
    @empty
        <p>No attendance records found for this period.</p>
    @endforelse

<script>
    (function() {
        var redirectUrl = document.body.getAttribute('data-redirect-url');
        var pageUnloading = false;
        
        // Open print immediately when page loads
        window.addEventListener('load', function() {
            window.print();
        }, { once: true });

        // Redirect when print dialog closes or is cancelled
        window.addEventListener('afterprint', function() {
            if (!pageUnloading) {
                pageUnloading = true;
                window.location.replace(redirectUrl);
            }
        }, { once: true });
        
        // Prevent re-triggering on unload
        window.addEventListener('beforeunload', function() {
            pageUnloading = true;
        });
    })();
</script>
</body>
</html>
