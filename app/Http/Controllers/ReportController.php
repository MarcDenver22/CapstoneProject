<div class="center subtle">Civil Service Form No. 48</div>
<div class="center title">DAILY TIME RECORD</div>
<div class="center subtle">-----o0o-----</div>

<div style="margin-top:8px;" class="center">
    <div class="line">{{ $employee->full_name ?? ($employee->name ?? '') }}</div><br>
    <span class="subtle">(Name)</span>
</div>

<div style="margin-top:8px;">
    <div>For the month of <span class="line">{{ $monthLabel }}</span></div>
    <div style="margin-top:4px;">
        Official hours for arrival and departure
        <span class="line" style="min-width:80px;"></span>
        Regular days <span class="line" style="min-width:60px;"></span>
        Saturdays <span class="line" style="min-width:60px;"></span>
    </div>
</div>

<table class="dtr thin">
    <thead>
    <tr>
        <th rowspan="2" style="width:26px;">Day</th>
        <th colspan="2">A.M.</th>
        <th colspan="2">P.M.</th>
        <th colspan="2">Undertime</th>
    </tr>
    <tr>
        <th style="width:55px;">Arrival</th>
        <th style="width:55px;">Departure</th>
        <th style="width:55px;">Arrival</th>
        <th style="width:55px;">Departure</th>
        <th style="width:45px;">Hours</th>
        <th style="width:45px;">Minutes</th>
    </tr>
    </thead>
    <tbody>
    @foreach($rows as $r)
        <tr class="{{ $r['enabled'] ? '' : 'disabled' }}">
            <td>{{ $r['day'] }}</td>
            <td>{{ $r['enabled'] ? $r['am_in'] : '' }}</td>
            <td>{{ $r['enabled'] ? $r['am_out'] : '' }}</td>
            <td>{{ $r['enabled'] ? $r['pm_in'] : '' }}</td>
            <td>{{ $r['enabled'] ? $r['pm_out'] : '' }}</td>
            <td>{{ $r['enabled'] ? $r['undertime_hours'] : '' }}</td>
            <td>{{ $r['enabled'] ? $r['undertime_minutes'] : '' }}</td>
        </tr>
    @endforeach
    <tr>
        <td colspan="5" class="center"><b>Total</b></td>
        <td></td>
        <td></td>
    </tr>
    </tbody>
</table>

<div class="footer-text muted">
    I certify on my honor that the above is a true and correct report of the hours of work performed, record of which was made daily at the time of arrival and departure from office.
</div>

<div class="signature">
    <div class="sigline"></div>
    <div class="center subtle">(Signature)</div>

    <div style="margin-top:10px;" class="center muted">VERIFIED as to the prescribed office hours:</div>
    <div class="sigline"></div>
    <div class="center subtle">In Charge</div>
</div>