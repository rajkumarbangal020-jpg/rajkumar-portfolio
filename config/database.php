<?php
require_once __DIR__ . '/config.php';

define('DB_HOST', getenv('PORTFOLIO_DB_HOST') ?: 'localhost');
define('DB_USER', getenv('PORTFOLIO_DB_USER') ?: 'root');
define('DB_PASS', getenv('PORTFOLIO_DB_PASS') ?: '');
define('DB_NAME', getenv('PORTFOLIO_DB_NAME') ?: 'rajkumar_portfolio');

function get_db_connection() {
    static $pdo = null;
    static $tried = false;
    if ($tried) return $pdo;
    $tried = true;
    try {
        $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
    } catch (PDOException $e) {
        $pdo = null;
    }
    return $pdo;
}

function is_db_connected() { return get_db_connection() instanceof PDO; }

function get_personal_info() {
    $db = get_db_connection();
    if ($db) {
        try { $r=$db->query('SELECT * FROM personal_information LIMIT 1')->fetch(); if($r) return $r; } catch(Exception $e) {}
    }
    return [
        'full_name'=>'Rajkumar Bangal','title'=>'Junior PHP Developer | CodeIgniter Developer | Full Stack Web Developer',
        'tagline'=>'I build secure, responsive and scalable web applications.','bio'=>'I am a PHP and CodeIgniter developer focused on database-driven business applications, admin panels, APIs and responsive web systems.',
        'location'=>'Kolkata, West Bengal, India','role'=>'Junior PHP Developer','availability'=>'Open to Opportunities',
        'preferred_locations'=>'Kolkata / Bengaluru / Hyderabad / Pune / Mumbai','email'=>'','phone'=>'','linkedin_url'=>'',
        'github_url'=>'https://github.com/rajkumarbangal020-jpg','resume_path'=>'assets/cv/rajkumar-bangal-cv.pdf'
    ];
}

function get_portfolio_skills() {
    $db=get_db_connection(); if($db){try{$r=$db->query('SELECT * FROM skills WHERE is_active=1 ORDER BY category,sort_order')->fetchAll();if($r)return $r;}catch(Exception $e){}}
    $items=[['Frontend','HTML5','fab fa-html5'],['Frontend','CSS3','fab fa-css3-alt'],['Frontend','JavaScript','fab fa-js'],['Frontend','Bootstrap 5','fab fa-bootstrap'],['Frontend','jQuery','fas fa-code'],['Backend','PHP 8','fab fa-php'],['Backend','CodeIgniter 3/4','fas fa-fire'],['Backend','MVC Architecture','fas fa-layer-group'],['Database','MySQL','fas fa-database'],['Tools','GitHub','fab fa-github'],['Learning','Laravel Framework','fab fa-laravel'],['Learning','Python','fab fa-python']];
    $out=[]; foreach($items as $i=>$x)$out[]=['id'=>$i+1,'category'=>$x[0],'name'=>$x[1],'icon_class'=>$x[2],'proficiency_level'=>'Proficient','sort_order'=>$i+1,'is_active'=>1]; return $out;
}

function get_portfolio_experiences() {
    $db=get_db_connection(); if($db){try{$r=$db->query('SELECT * FROM experiences ORDER BY sort_order')->fetchAll();if($r)return $r;}catch(Exception $e){}}
    return [
      ['id'=>1,'company'=>'WEBANQUETS.IN','role'=>'IT Intern / Website Developer','duration'=>'December 2025 – April 2026','is_current'=>0,'responsibilities'=>json_encode(['Web development and technical support','PHP/MySQL application maintenance','Bug fixing and system maintenance'])],
      ['id'=>2,'company'=>'IdeaPick','role'=>'PHP Developer (Part-time)','duration'=>'2026 – Present','is_current'=>1,'responsibilities'=>json_encode(['Referral and wallet modules','PHP/MySQL backend development','Admin panel and payout workflows'])]
    ];
}

function get_portfolio_projects() {
    $db=get_db_connection(); if($db){try{$r=$db->query('SELECT * FROM projects WHERE is_active=1 ORDER BY sort_order')->fetchAll();if($r)return $r;}catch(Exception $e){}}
    $p=[
      ['School Management System','school_management.png','PHP & CodeIgniter','PHP, CodeIgniter, MySQL, Bootstrap 5, JavaScript','Role-based school management with attendance, QR student IDs and subscription plans.'],
      ['Employee Attendance & PWA System','employee_attendance.png','Web Apps & PWA','PHP, CodeIgniter, MySQL, JavaScript, PWA','GPS and selfie based employee attendance with PWA support.'],
      ['Restaurant Billing & Management System','restaurant_billing.png','PHP & CodeIgniter','PHP, CodeIgniter, MySQL, Bootstrap 5','Restaurant billing, menu, subscription and reporting system.'],
      ['Referral Wallet & Payout System','referral_wallet.png','Management Systems','PHP, MySQL, JavaScript, Bootstrap 5, jQuery','Referral rewards, wallet ledger, bank/UPI verification and payouts.'],
      ['AI Voice Calling System – Risha','ai_voice_calling.png','Full Stack & APIs','Python, PHP, CodeIgniter, MySQL, Deepgram API','Multilingual AI voice calling and call automation platform.'],
      ['Catering & Event Management Website','catering_event.png','Full Stack & APIs','PHP, MySQL, Bootstrap 5, JavaScript, jQuery','Dynamic catering website with gallery, reviews and enquiry management.']
    ];
    $out=[]; foreach($p as $i=>$x)$out[]=['id'=>$i+1,'title'=>$x[0],'slug'=>strtolower(str_replace(' ','-',$x[0])),'short_description'=>$x[4],'full_description'=>$x[4],'problem_solved'=>'','challenges_solutions'=>'','my_responsibilities'=>'','features'=>json_encode([]),'technologies'=>$x[3],'thumbnail'=>'uploads/projects/'.$x[1],'live_url'=>'#','github_url'=>'#','category'=>$x[2],'sort_order'=>$i+1,'is_featured'=>1,'is_active'=>1]; return $out;
}

function get_portfolio_education() {
    $db=get_db_connection(); if($db){try{$r=$db->query('SELECT * FROM education ORDER BY sort_order')->fetchAll();if($r)return $r;}catch(Exception $e){}}
    return [['id'=>1,'degree'=>'Bachelor of Commerce (B.Com)','institution'=>'Joypur Panchanan Roy College','field_of_study'=>'Commerce','start_year'=>'2023','end_year'=>'2026','status'=>'Pursuing','grade_marks'=>'Ongoing','sort_order'=>1]];
}

function get_portfolio_certifications() {
    $db=get_db_connection(); if($db){try{$r=$db->query('SELECT * FROM certifications ORDER BY sort_order')->fetchAll();if($r)return $r;}catch(Exception $e){}}
    $names=[['PHP Developer Course','PHP Kolkata'],['Diploma in Financial Accounting & E-Filing','WEBEL'],['DOEACC / NIELIT O Level','NIELIT'],['Data Entry Operations','Utkarsh Bangla']];
    $o=[];foreach($names as $i=>$n)$o[]=['id'=>$i+1,'title'=>$n[0],'issuing_organization'=>$n[1],'issue_year'=>'','credential_url'=>'#','certificate_file'=>'#','sort_order'=>$i+1];return $o;
}
