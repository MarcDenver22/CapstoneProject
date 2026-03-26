<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 2.54cm; size: 21cm 29.7cm; }
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #000; font-size: 10px; margin: 0; padding: 0; line-height: 1.2; }

        /* two column layout - A4 portrait */
        .two-col { display: table; width: 100%; table-layout: fixed; margin: 0 auto; }
        .col { display: table-cell; width: 50%; vertical-align: top; padding: 1px 0.5px; text-align: center; }

        .form-block { padding: 1px 0.5px; margin: 0 auto; width: 90%; }

        .center { text-align: center; margin: 0; }
        .small { font-size: 8px; }
        .title { font-size: 11px; font-weight: 700; letter-spacing: 0; margin: 0; }
        .header { font-size: 8px; margin: 0; }

        /* underlines - using underscores for alignment */
        .u-line {
            display: inline-block;
            border-bottom: 1px solid #000;
            vertical-align: baseline;
            font-size: 8px;
            padding: 0 2px 1px 2px;
        }
        .u-name { min-width: 150px; }
        .u-month { min-width: 60px; }
        .u-hours { min-width: 50px; }
        .u-days { min-width: 25px; }

        /* table - highly compressed for portrait A4 */
        table.form { 
            width: 92%; 
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
            height: 8px;
        }
        table.form th { font-weight: 700; font-size: 8px; }

        /* column widths - matching form proportions */
        .w-day { width: 8%; }
        .w-time { width: 15%; }
        .w-und { width: 10%; }

        .total-row td { font-weight: 700; }

        .cert { font-size: 7px; margin: 0.5px 0 0 0; text-align: center; line-height: 1.2; padding: 0; }
        .sigline { border-bottom: 1px solid #000; width: 50%; height: 5px; margin: 1px auto 0; }
        .verified { margin-top: 0.5px; font-size: 7px; text-align: center; }
        .sig-label { font-size: 7px; text-align: center; margin-top: 0.3px; }
    </style>
</head>
<body>
{!! $html !!}
</body>
</html>
