<?php
// api/export.php — Export events report as a real Excel (.xlsx) file
// Uses SpreadsheetML XML format — no external library required
require_once '../config/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$startDate = $_GET['startDate'] ?? date('Y-m-01');
$endDate   = $_GET['endDate']   ?? date('Y-m-t');
$venueId   = $_GET['venueId']   ?? '';

$params      = [$startDate, $endDate];
$venueFilter = '';
if (!empty($venueId)) {
    $venueFilter = 'AND es.VenueID = ?';
    $params[]    = $venueId;
}

try {
    // ── Fetch events ───────────────────────────────────────────────
    $stmt = $pdo->prepare("
        SELECT
            e.EventID,
            e.Title             AS EventName,
            e.Description,
            e.Status,
            e.CapacityLimit     AS AttendeeLimit,
            u.Name              AS Organizer,
            v.Name              AS Venue,
            t.EventDate,
            t.StartTime,
            t.EndTime,
            (SELECT COUNT(*) FROM booking b
             WHERE b.EventID = e.EventID
               AND b.Status IN ('PENDING','APPROVED')) AS Registered
        FROM event e
        LEFT JOIN user u            ON e.OrganizerID = u.UserID
        LEFT JOIN event_schedule es ON e.EventID     = es.EventID
        LEFT JOIN venue v           ON es.VenueID    = v.VenueID
        LEFT JOIN timeslot t        ON es.SlotID     = t.SlotID
        WHERE t.EventDate BETWEEN ? AND ?
          AND e.Status != 'CANCELLED'
          $venueFilter
        GROUP BY e.EventID, e.Title, e.Description, e.Status, e.CapacityLimit,
                 u.Name, v.Name, t.EventDate, t.StartTime, t.EndTime
        ORDER BY t.EventDate ASC, t.StartTime ASC
    ");
    $stmt->execute($params);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ── Helper: escape value for XML cell ─────────────────────────
    function xlCell($value, $type = 'String')
    {
        $safe = htmlspecialchars((string)$value, ENT_XML1, 'UTF-8');
        return "<Cell><Data ss:Type=\"{$type}\">{$safe}</Data></Cell>";
    }

    // ── Build SpreadsheetML XML ────────────────────────────────────
    $filename = 'CelebrateHub_Events_' . $startDate . '_to_' . $endDate . '.xlsx';

    // Column headers
    $headers = [
        'Event ID',
        'Event Name',
        'Description',
        'Status',
        'Attendee Limit',
        'Registered',
        'Fill %',
        'Organizer',
        'Venue',
        'Date',
        'Start Time',
        'End Time'
    ];

    // Header row XML
    $headerRow = '<Row ss:StyleID="header">';
    foreach ($headers as $h) {
        $headerRow .= xlCell($h);
    }
    $headerRow .= '</Row>';

    // Data rows XML
    $dataRows = '';
    foreach ($events as $ev) {
        $fillPct = $ev['AttendeeLimit'] > 0
            ? round(($ev['Registered'] / $ev['AttendeeLimit']) * 100, 1)
            : 0;

        $rowStyle = $fillPct >= 85 ? ' ss:StyleID="warn"' : '';

        $dataRows .= "<Row{$rowStyle}>";
        $dataRows .= xlCell($ev['EventID'],      'Number');
        $dataRows .= xlCell($ev['EventName']);
        $dataRows .= xlCell($ev['Description'] ?? '');
        $dataRows .= xlCell($ev['Status']);
        $dataRows .= xlCell($ev['AttendeeLimit'], 'Number');
        $dataRows .= xlCell($ev['Registered'],    'Number');
        $dataRows .= xlCell($fillPct . '%');
        $dataRows .= xlCell($ev['Organizer'] ?? '—');
        $dataRows .= xlCell($ev['Venue']     ?? '—');
        $dataRows .= xlCell($ev['EventDate'] ?? '');
        $dataRows .= xlCell(substr($ev['StartTime'] ?? '', 0, 5));
        $dataRows .= xlCell(substr($ev['EndTime']   ?? '', 0, 5));
        $dataRows .= '</Row>';
    }

    $generatedOn = date('Y-m-d H:i');
    $totalEvents = count($events);

    $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
          xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
          xmlns:x="urn:schemas-microsoft-com:office:excel">

  <Styles>
    <Style ss:ID="default">
      <Font ss:FontName="Calibri" ss:Size="11"/>
    </Style>
    <Style ss:ID="header">
      <Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1" ss:Color="#FFFFFF"/>
      <Interior ss:Color="#2D3A8C" ss:Pattern="Solid"/>
      <Alignment ss:Horizontal="Center"/>
    </Style>
    <Style ss:ID="warn">
      <Font ss:FontName="Calibri" ss:Size="11"/>
      <Interior ss:Color="#FFF3CD" ss:Pattern="Solid"/>
    </Style>
    <Style ss:ID="title">
      <Font ss:FontName="Calibri" ss:Size="14" ss:Bold="1"/>
    </Style>
    <Style ss:ID="meta">
      <Font ss:FontName="Calibri" ss:Size="10" ss:Color="#666666"/>
    </Style>
  </Styles>

  <Worksheet ss:Name="Events Report">
    <Table>
      <Column ss:Width="60"/>
      <Column ss:Width="180"/>
      <Column ss:Width="200"/>
      <Column ss:Width="90"/>
      <Column ss:Width="90"/>
      <Column ss:Width="90"/>
      <Column ss:Width="60"/>
      <Column ss:Width="130"/>
      <Column ss:Width="140"/>
      <Column ss:Width="90"/>
      <Column ss:Width="80"/>
      <Column ss:Width="80"/>

      <Row>
        <Cell ss:StyleID="title"><Data ss:Type="String">CelebrateHub — Events Report</Data></Cell>
      </Row>
      <Row>
        <Cell ss:StyleID="meta"><Data ss:Type="String">Period: {$startDate} to {$endDate}</Data></Cell>
      </Row>
      <Row>
        <Cell ss:StyleID="meta"><Data ss:Type="String">Total Events: {$totalEvents}  |  Generated: {$generatedOn}</Data></Cell>
      </Row>
      <Row/>

      {$headerRow}
      {$dataRows}

    </Table>
  </Worksheet>
</Workbook>
XML;

    // ── Send as Excel file ─────────────────────────────────────────
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    echo $xml;
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
