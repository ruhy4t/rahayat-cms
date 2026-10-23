<?php
declare(strict_types=1);
require __DIR__ . '/security-bootstrap.php';
require_once APP_PATH . '/Core/Security.php';
$output = STORAGE_PATH . '/cache/theme-preview';
if (!is_dir($output)) { mkdir($output, 0755, true); }
$profile = ['name'=>'Sekolah Uji Nusantara','motto'=>'Berilmu, Berkarakter, Berprestasi','tagline'=>'Belajar untuk masa depan','address'=>'Alamat sekolah pengujian','phone'=>'021123456','email'=>'sekolah@example.invalid','vision'=>'<p>Pendidikan yang berkualitas untuk semua.</p>','mission'=>'<p>Belajar dan mengembangkan potensi.</p>','history'=>'<p>Riwayat sekolah dari data admin.</p>','total_students'=>1024,'total_teachers'=>78,'accreditation'=>'A','graduation_rate'=>98,'npsn'=>'00000000','principal_name'=>'Kepala Sekolah Uji'];
$settings = ['whatsapp_enabled'=>'0','footer_text'=>'© {year} {school}','principal_message_enabled'=>'0'];
$headerMenus = [['title'=>'Beranda','url'=>'/','children'=>[]],['title'=>'Profil','url'=>'/profil','children'=>[['title'=>'Visi & Misi','url'=>'/profil#profile-vision','children'=>[]],['title'=>'GTK','url'=>'/gtk','children'=>[]]]],['title'=>'Informasi','url'=>'/berita','children'=>[['title'=>'Berita','url'=>'/berita','children'=>[]],['title'=>'Agenda','url'=>'/agenda','children'=>[]],['title'=>'Kalender','url'=>'/kalender-pendidikan','children'=>[]]]],['title'=>'Galeri','url'=>'/galeri','children'=>[]],['title'=>'Kontak','url'=>'/kontak','children'=>[]]];
$footerMenus = $headerMenus;
$event = ['id'=>1,'title'=>'Kegiatan akademik pengujian','category'=>'kegiatan','start_date'=>'2026-10-23','end_date'=>'2026-10-24','event_time'=>'08:00','location'=>'Aula sekolah','description'=>'Deskripsi kegiatan dari admin.','kind'=>'agenda','start_year'=>2026];
$item = ['id'=>1,'slug'=>'berita-pengujian','title'=>'Prestasi dan kegiatan sekolah yang menginspirasi','excerpt'=>'Informasi singkat kegiatan belajar dari pengelola sekolah.','content'=>'<p>Konten artikel yang dapat dikelola melalui admin.</p>','category'=>'Kegiatan','image'=>'','published_at'=>'2026-10-23','created_at'=>'2026-10-23','description'=>'Deskripsi dari admin.','date'=>'2026-10-23'];
$news = array_fill(0,4,$item); $upcomingEvents=[$event]; $slides=[]; $facilities=[['name'=>'Perpustakaan','description'=>'Fasilitas belajar','image'=>'']]; $ekskul=[]; $galleryItems=[]; $testimonials=[]; $calendarData=['years'=>[]]; $spmbPublic=['active'=>false]; $activePopup=null; $activeTextSlides=[]; $visitorStatistics=null;
$groupedStaff=['teachers'=>['name'=>'Guru','items'=>[['name'=>'Guru Pengujian','position'=>'Pendidik','photo'=>'']]]];
$albums=[['id'=>1,'slug'=>'album-uji','title'=>'Kegiatan Sekolah','description'=>'Dokumentasi dari admin','item_count'=>4,'cover_image'=>'']];
$events=[$event]; $archive=false; $page=1; $related=[]; $pagination=[];
$startDate='2026-01-01'; $endDate='2026-12-31'; $quota=0; $totalRegistered=0;
$calendarYearFixture=['id'=>1,'start_year'=>2026,'is_published'=>1,'pdf_path'=>''];
$calendarData=['year'=>$calendarYearFixture,'years'=>[$calendarYearFixture],'month'=>'2026-10','months'=>AcademicCalendar::months(2026),'events'=>[$event]];
$searched=false; $registration=null; $submissionToken=str_repeat('a',64); $selectedDocuments=[];
$themes=(new SiteSetting())->getAvailableThemes();
$sampleItem=$item;
$pages=['home'=>'frontend/home.php','profile'=>'frontend/next/profile.php','news'=>'frontend/next/news-index.php','article'=>'frontend/next/news-show.php','gallery'=>'frontend/next/gallery.php','gtk'=>'frontend/next/gtk.php','contact'=>'frontend/next/contact.php','agenda'=>'frontend/next/agenda.php','calendar'=>'frontend/next/calendar.php','achievements'=>'frontend/next/achievements.php'];
$pages += ['album'=>'frontend/next/gallery-detail.php','event'=>'frontend/next/event-detail.php','achievement'=>'frontend/next/achievement-detail.php','program'=>'frontend/next/program-detail.php','principal'=>'frontend/next/principal.php','alumni-detail'=>'frontend/next/alumni-detail.php','testimonials'=>'frontend/testimonials.php','alumni'=>'frontend/alumni/index.php','spmb'=>'frontend/spmb/index.php','register'=>'frontend/spmb/register.php','status'=>'frontend/spmb/status.php'];
$pages['testimonials']='frontend/next/testimonials.php';
$pages['alumni']='frontend/next/alumni.php';
foreach (PublicTheme::NAMES as $themeName) {
    $theme=$themeName; $themeConfig=$themes[$themeName];
    foreach ($pages as $pageName=>$template) {
        $item=$sampleItem;
        $news=$pageName==='article'?$item:array_fill(0,4,$item);
        $title=ucfirst($pageName); $pageHeading=null;
        $data=compact('profile','settings','theme','news','slides','facilities','ekskul','testimonials','upcomingEvents','calendarData','groupedStaff','albums');
        $data['prestasi']=[$item];
        $data['prestasiItem']=$item; $data['ekskulItem']=['name'=>'Program pengembangan murid','description'=>'Kegiatan dari admin.'];
        $data['alumni']=['name'=>'Alumni Pengujian','graduation_year'=>2020,'story'=>'Cerita alumni yang disetujui.'];
        $album=$albums[0]; $items=[];
        $_SERVER['REQUEST_URI']=$pageName==='home'?'/':'/'.$pageName;
        ob_start(); require VIEW_PATH.'/'.$template; $content=ob_get_clean();
        ob_start(); require VIEW_PATH.'/layouts/frontend.php'; $html=ob_get_clean();
        $html=str_replace('<head>','<head><base href="http://rahayat-cms.test/">',$html);
        $dom=new DOMDocument(); @$dom->loadHTML($html); $xpath=new DOMXPath($dom);
        if ($xpath->query('//header[contains(@class,"next-header")]')->length !== 1) { throw new RuntimeException('Header missing: '.$themeName.'/'.$pageName); }
        if ($xpath->query('//nav[contains(@class,"next-desktop-nav")]//a[@href="/profil"]')->length !== 1) { throw new RuntimeException('Parent link duplicated or missing'); }
        if ($xpath->query('//*[contains(@class,"next-submenu")]/a[@href="/profil"]')->length !== 0) { throw new RuntimeException('Duplicate parent in submenu'); }
        if (str_contains($html,'nav-cendekia') || str_contains($html,'cendekia-header')) { throw new RuntimeException('Legacy header leaked'); }
        if ($pageName === 'home') {
            $stats=$xpath->query('//div[contains(@class,"next-stat")]/strong');
            $values=[]; foreach ($stats as $stat) { $values[]=trim($stat->textContent); }
            if ($values!==['1024+','78+','A','98%']) { throw new RuntimeException('Statistic units are incorrect: '.json_encode($values)); }
        }
        file_put_contents($output.'/'.$themeName.'-'.$pageName.'.html',$html);
    }
}
echo 'Rendered '.(count($pages)*count(PublicTheme::NAMES))." synthetic pages in storage/cache/theme-preview.\n";
