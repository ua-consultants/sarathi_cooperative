<?php
// scheduled_announcements.php - Generate recurring announcements
$conn = new mysqli('localhost', 'u828878874_sarathi_new', '#Sarathi@2025', 'u828878874_sarathi_db');

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}
require_once 'send_announcement.php';

function calculateNextBODMeeting() {
    $year = date('Y');
    $month = date('n');
    
    // Find 2nd Saturday of current month
    $first_day = mktime(0, 0, 0, $month, 1, $year);
    $first_saturday = date('j', strtotime('first Saturday', $first_day));
    $second_saturday = $first_saturday + 7;
    
    $meeting_date = mktime(0, 0, 0, $month, $second_saturday, $year);
    
    // If the meeting date has passed, calculate for next month
    if ($meeting_date < time()) {
        $month = ($month == 12) ? 1 : $month + 1;
        $year = ($month == 1) ? $year + 1 : $year;
        
        $first_day = mktime(0, 0, 0, $month, 1, $year);
        $first_saturday = date('j', strtotime('first Saturday', $first_day));
        $second_saturday = $first_saturday + 7;
        $meeting_date = mktime(0, 0, 0, $month, $second_saturday, $year);
    }
    
    return $meeting_date;
}

function calculateNextWebinar() {
    $year = date('Y');
    $month = date('n');
    
    // Find last Saturday of current month
    $last_day = date('t', mktime(0, 0, 0, $month, 1, $year));
    $last_day_timestamp = mktime(0, 0, 0, $month, $last_day, $year);
    
    // Find the last Saturday
    $last_saturday_day = $last_day;
    while (date('w', mktime(0, 0, 0, $month, $last_saturday_day, $year)) != 6) {
        $last_saturday_day--;
    }
    
    $webinar_date = mktime(0, 0, 0, $month, $last_saturday_day, $year);
    
    // If the webinar date has passed, calculate for next month
    if ($webinar_date < time()) {
        $month = ($month == 12) ? 1 : $month + 1;
        $year = ($month == 1) ? $year + 1 : $year;
        
        $last_day = date('t', mktime(0, 0, 0, $month, 1, $year));
        $last_saturday_day = $last_day;
        while (date('w', mktime(0, 0, 0, $month, $last_saturday_day, $year)) != 6) {
            $last_saturday_day--;
        }
        $webinar_date = mktime(0, 0, 0, $month, $last_saturday_day, $year);
    }
    
    return $webinar_date;
}

function calculateNextQuarterlyMeeting() {
    $current_month = date('n');
    $current_year = date('Y');
    
    // Determine the last month of current quarter
    $quarter_end_months = [3, 6, 9, 12];
    $next_quarter_end = null;
    
    foreach ($quarter_end_months as $end_month) {
        if ($end_month >= $current_month) {
            $next_quarter_end = $end_month;
            break;
        }
    }
    
    // If no quarter end found in current year, use March of next year
    if ($next_quarter_end === null) {
        $next_quarter_end = 3;
        $current_year++;
    }
    
    // Find 2nd Sunday of the quarter end month
    $first_day = mktime(0, 0, 0, $next_quarter_end, 1, $current_year);
    $first_sunday = date('j', strtotime('first Sunday', $first_day));
    $second_sunday = $first_sunday + 7;
    
    return mktime(0, 0, 0, $next_quarter_end, $second_sunday, $current_year);
}

function sendBODMeetingAnnouncement() {
    $meeting_date = calculateNextBODMeeting();
    $formatted_date = date('l, F j, Y', $meeting_date);
    
    $title = "Board of Directors Meeting - " . date('F Y', $meeting_date);
    $content = "
        <h3>Board of Directors Meeting</h3>
        <p><strong>Date:</strong> $formatted_date</p>
        <p><strong>Time:</strong> 10:00 AM</p>
        <p><strong>Venue:</strong> Sarathi Cooperative Main Office</p>
        <p>Dear Members,</p>
        <p>You are cordially invited to attend the monthly Board of Directors meeting. The agenda will include:</p>
        <ul>
            <li>Review of previous month's activities</li>
            <li>Financial updates and reports</li>
            <li>New business proposals</li>
            <li>Member queries and suggestions</li>
        </ul>
        <p>For any queries, please contact the office.</p>
        <p>Best regards,<br>Sarathi Cooperative Team</p>
    ";
    
    return sendAnnouncementToSubscribers($title, $content, 'bod_meeting');
}

function sendWebinarAnnouncement() {
    $webinar_date = calculateNextWebinar();
    $formatted_date = date('l, F j, Y', $webinar_date);
    
    $title = "Monthly Online Webinar - " . date('F Y', $webinar_date);
    $content = "
        <h3>Monthly Online Webinar</h3>
        <p><strong>Date:</strong> $formatted_date</p>
        <p><strong>Time:</strong> 7:00 PM</p>
        <p><strong>Platform:</strong> Zoom (Link will be shared via email)</p>
        <p>Dear Members,</p>
        <p>Join us for our monthly online webinar where we will discuss:</p>
        <ul>
            <li>Cooperative updates and news</li>
            <li>Financial literacy and investment tips</li>
            <li>Q&A session with the management</li>
            <li>Member success stories</li>
        </ul>
        <p>Registration is free for all members. The Zoom link will be sent to registered participants 24 hours before the event.</p>
        <p>To register, please reply to this email or contact our office.</p>
        <p>Best regards,<br>Sarathi Cooperative Team</p>
    ";
    
    return sendAnnouncementToSubscribers($title, $content, 'webinar');
}

function sendQuarterlyMeetingAnnouncement() {
    $meeting_date = calculateNextQuarterlyMeeting();
    $formatted_date = date('l, F j, Y', $meeting_date);
    $quarter = ceil(date('n', $meeting_date) / 3);
    
    $title = "Quarterly General Body Meeting - Q$quarter " . date('Y', $meeting_date);
    $content = "
        <h3>Quarterly General Body Meeting</h3>
        <p><strong>Date:</strong> $formatted_date</p>
        <p><strong>Time:</strong> 10:00 AM</p>
        <p><strong>Venue:</strong> Sarathi Cooperative Main Hall</p>
        <p>Dear Valued Members,</p>
        <p>You are cordially invited to attend our Quarterly General Body Meeting for Q$quarter " . date('Y', $meeting_date) . ".</p>
        <p><strong>Agenda:</strong></p>
        <ul>
            <li>Quarterly financial report presentation</li>
            <li>Review of cooperative performance</li>
            <li>New policy announcements</li>
            <li>Election of board members (if applicable)</li>
            <li>Member feedback and suggestions</li>
            <li>Future plans and initiatives</li>
        </ul>
        <p>Your presence is highly valued as we discuss the progress and future direction of our cooperative.</p>
        <p>Light refreshments will be served.</p>
        <p>Best regards,<br>Sarathi Cooperative Management</p>
    ";
    
    return sendAnnouncementToSubscribers($title, $content, 'quarterly_meeting');
}

// Check if this is a cron job execution
if (isset($_GET['cron']) && $_GET['cron'] === 'true') {
    $results = [];
    
    // Check if we need to send BOD meeting announcement (send 1 week before)
    $next_bod = calculateNextBODMeeting();
    $days_until_bod = ceil(($next_bod - time()) / (24 * 60 * 60));
    
    if ($days_until_bod == 7) {
        $results['bod'] = sendBODMeetingAnnouncement();
    }
    
    // Check if we need to send webinar announcement (send 3 days before)
    $next_webinar = calculateNextWebinar();
    $days_until_webinar = ceil(($next_webinar - time()) / (24 * 60 * 60));
    
    if ($days_until_webinar == 3) {
        $results['webinar'] = sendWebinarAnnouncement();
    }
    
    // Check if we need to send quarterly meeting announcement (send 2 weeks before)
    $next_quarterly = calculateNextQuarterlyMeeting();
    $days_until_quarterly = ceil(($next_quarterly - time()) / (24 * 60 * 60));
    
    if ($days_until_quarterly == 14) {
        $results['quarterly'] = sendQuarterlyMeetingAnnouncement();
    }
    
    echo json_encode($results);
}
?>
