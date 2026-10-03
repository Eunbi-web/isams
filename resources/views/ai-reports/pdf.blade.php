<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>{{ $report->title }}</title>
<style>
    body { font-family: Georgia, 'Times New Roman', serif; font-size: 11px; color: #1c1c1c; line-height: 1.55; }
    .doc-title { font-size: 18px; text-align: center; margin: 0 0 4px; color: #12355f; }
    .sub { font-size: 9px; color: #666; text-align: center; margin-bottom: 4px; }
    .rule { border-bottom: 2px solid #12355f; margin-bottom: 14px; }
    h2 { font-size: 13.5px; color: #12355f; border-bottom: 1px solid #b9c6d8; padding-bottom: 3px; margin: 16px 0 7px; }
    h3 { font-size: 12px; color: #12355f; margin: 12px 0 5px; }
    p { margin: 0 0 9px; text-align: justify; }
    table { width: 100%; border-collapse: collapse; margin: 8px 0 12px; }
    th { background: #e8eef7; border: 1px solid #9db0cb; padding: 4px 6px; font-size: 9px; text-transform: uppercase; letter-spacing: .4px; text-align: left; color: #12355f; }
    td { border: 1px solid #c3cfdf; padding: 4px 6px; font-size: 9.5px; }
    ul, ol { margin: 0 0 10px 20px; padding: 0; }
    li { margin-bottom: 4px; }
    .footer { margin-top: 18px; border-top: 1px solid #ccc; padding-top: 6px; font-size: 8.5px; color: #888; text-align: center; }
</style>
</head>
<body>
<h1 class="doc-title">{{ $report->title }}</h1>
<div class="sub">Saint Columban College &middot; Pagadian City &middot; A.Y. {{ $report->academic_year }}, {{ $report->semester }}</div>
<div class="sub">Generated {{ $report->created_at->format('F j, Y g:i A') }} &middot; Prepared by {{ $report->prepared_by }}</div>
<div class="rule"></div>

{!! $report->content_html !!}

<div class="footer">ISAMS — Integrated Student Affairs Management System &middot; AI-assisted report generated with Groq and reviewed by the office of record.</div>
</body>
</html>
