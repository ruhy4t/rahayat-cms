<?php
declare(strict_types=1);
require __DIR__ . '/security-bootstrap.php';
require_once APP_PATH . '/Core/Security.php';
$output = STORAGE_PATH . '/cache/theme-preview';
if (!is_dir($output)) { mkdir($output, 0755, true); }
$illustration='<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="700" viewBox="0 0 1200 700"><rect width="1200" height="700" fill="#dbeafe"/><circle cx="960" cy="130" r="70" fill="#fde68a"/><path d="M0 550Q300 420 600 560T1200 490V700H0Z" fill="#86a99b"/><path d="M160 310L600 120L1040 310Z" fill="#334155"/><rect x="200" y="310" width="800" height="280" fill="#f8fafc"/><g fill="#5a7c98"><path d="M240 350H350V440H240ZM410 350H520V440H410ZM680 350H790V440H680ZM850 350H960V440H850Z"/></g><path d="M560 410H640V590H560Z" fill="#334155"/><rect x="150" y="590" width="900" height="20" fill="#64748b"/></svg>';
file_put_contents($output.'/school-fixture.svg',$illustration);
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
$sampleItem['image']='__theme-fixture.svg';
$slides=[['image'=>'__theme-fixture.svg']];
$profile['principal_photo'] = '__theme-fixture.svg';
$profile['welcome_message'] = '<p>Sambutan kepala sekolah yang dikelola melalui admin.</p>';
$settings['principal_message_enabled'] = '1';
$settings['principal_message_title'] = 'Sambutan Kepala Sekolah';
$settings['principal_message_summary'] = 'Selamat datang. Bersama keluarga sekolah, kami membangun lingkungan belajar yang aman, inklusif, dan bermakna.';
$pages=['home'=>'frontend/home.php','profile'=>'frontend/next/profile.php','news'=>'frontend/next/news-index.php','article'=>'frontend/next/news-show.php','gallery'=>'frontend/next/gallery.php','gtk'=>'frontend/next/gtk.php','contact'=>'frontend/next/contact.php','agenda'=>'frontend/next/agenda.php','calendar'=>'frontend/next/calendar.php','achievements'=>'frontend/next/achievements.php'];
$pages += ['album'=>'frontend/next/gallery-detail.php','event'=>'frontend/next/event-detail.php','achievement'=>'frontend/next/achievement-detail.php','program'=>'frontend/next/program-detail.php','principal'=>'frontend/next/principal.php','alumni-detail'=>'frontend/next/alumni-detail.php','testimonials'=>'frontend/testimonials.php','alumni'=>'frontend/alumni/index.php','spmb'=>'frontend/spmb/index.php','register'=>'frontend/spmb/register.php','status'=>'frontend/spmb/status.php'];
$pages['testimonials']='frontend/next/testimonials.php';
$pages['alumni']='frontend/next/alumni.php';
foreach (PublicTheme::NAMES as $themeName) {
    // Cover editor HTML and legacy entity-encoded content without modifying DB.
    $mottoHtml = '<p><strong>Berilmu, Berkarakter, Berprestasi</strong></p>';
    $profile['motto'] = in_array($themeName, ['arunika-emerald', 'samudra-cendekia'], true)
        ? htmlspecialchars($mottoHtml, ENT_QUOTES | ENT_HTML5, 'UTF-8') : $mottoHtml;
    $sampleItem['excerpt'] = '&lt;p&gt;Ringkasan &lt;strong&gt;kegiatan sekolah&lt;/strong&gt;&lt;/p&gt;';
    $facilities[0]['description'] = '&lt;p&gt;Fasilitas &lt;strong&gt;belajar&lt;/strong&gt;&lt;/p&gt;';
    $theme=$themeName; $themeConfig=$themes[$themeName];
    foreach ($pages as $pageName=>$template) {
        $item=$sampleItem;
        $news=$pageName==='article'?$item:array_fill(0,4,$item);
        $title=ucfirst($pageName); $pageHeading=null;
        $data=compact('profile','settings','theme','news','slides','facilities','ekskul','testimonials','upcomingEvents','calendarData','groupedStaff','albums');
        $data['prestasi']=[$item];
        $data['prestasiItem']=$item; $data['ekskulItem']=['name'=>'Program pengembangan murid','description'=>'Kegiatan dari admin.'];
        $data['alumni']=['name'=>'Alumni Pengujian','graduation_year'=>2020,'story'=>'Cerita alumni yang disetujui.'];
        $album=$albums[0]; $items=$pageName==='album'?[['type'=>'image','file_path'=>'__theme-fixture.svg','title'=>'Kegiatan pengujian satu'],['type'=>'image','file_path'=>'__theme-fixture.svg','title'=>'Kegiatan pengujian dua']]:[];
        $_SERVER['REQUEST_URI']=$pageName==='home'?'/':'/'.$pageName;
        ob_start(); require VIEW_PATH.'/'.$template; $content=ob_get_clean();
        ob_start(); require VIEW_PATH.'/layouts/frontend.php'; $html=ob_get_clean();
        $html=str_replace('<head>','<head><base href="http://rahayat-cms.test/">',$html);
        $html=str_replace('/storage/__theme-fixture.svg','file:///' . str_replace('\\','/',$output) . '/school-fixture.svg',$html);
        $dom=new DOMDocument(); @$dom->loadHTML($html); $xpath=new DOMXPath($dom);
        if ($xpath->query('//header[contains(@class,"next-header")]')->length !== 1) { throw new RuntimeException('Header missing: '.$themeName.'/'.$pageName); }
        if ($xpath->query('//nav[contains(@class,"next-desktop-nav")]//a[@href="/profil"]')->length !== 1) { throw new RuntimeException('Parent link duplicated or missing'); }
        if ($xpath->query('//*[contains(@class,"next-submenu")]/a[@href="/profil"]')->length !== 0) { throw new RuntimeException('Duplicate parent in submenu'); }
        if (str_contains($html,'nav-cendekia') || str_contains($html,'cendekia-header')) { throw new RuntimeException('Legacy header leaked'); }
        if ($pageName === 'album' && (
            $xpath->query('//button[@data-gallery-image and @aria-controls="next-gallery-viewer" and not(@target)]')->length !== 2
            || $xpath->query('//dialog[@id="next-gallery-viewer"]')->length !== 1
        )) { throw new RuntimeException('Gallery must use same-page dialog triggers'); }
        foreach ($xpath->query('//body//text()[not(ancestor::script) and not(ancestor::style) and not(ancestor::textarea)]') as $textNode) {
            if (preg_match('/<\/?(?:p|strong|span|div|br)\b/i', $textNode->textContent)) {
                throw new RuntimeException('HTML published as visible text: ' . $themeName . '/' . $pageName);
            }
        }
        if (!str_contains($xpath->query('//header[contains(@class,"next-header")]')->item(0)->textContent, 'Berilmu, Berkarakter, Berprestasi')) {
            throw new RuntimeException('School motto missing from header');
        }
        if ($pageName === 'home') {
            if ($xpath->query('//*[contains(@class,"next-principal-block")]//*[@id="principal-message-title"]')->length !== 1) { throw new RuntimeException('Compact principal card missing'); }
            $expectedLayout=['nawala-indigo'=>'portal','arunika-emerald'=>'campus','wibawa-crimson'=>'editorial','samudra-cendekia'=>'explore'][$themeName];
            if ($xpath->query('//*[@data-home-layout="'.$expectedLayout.'"]')->length!==1) { throw new RuntimeException('Distinct theme composition missing'); }
            $stats=$xpath->query('//div[contains(@class,"next-stat")]/strong');
            $values=[]; foreach ($stats as $stat) { $values[]=trim($stat->textContent); }
            if ($values!==['1024+','78+','A','98%']) { throw new RuntimeException('Statistic units are incorrect: '.json_encode($values)); }
        }
        file_put_contents($output.'/'.$themeName.'-'.$pageName.'.html',$html);
    }
}
(static function () use ($output): void {
    $model = new SiteSetting();
    $data = ['title'=>'Pengaturan', 'settings'=>[], 'themes'=>$model->getAvailableThemes(),
        'currentTheme'=>'arunika-emerald', 'fonts'=>$model->getAvailableFonts(),
        'profile'=>['name'=>'Sekolah Uji', 'phone'=>'021123456'], 'flash'=>null];
    $user = ['id'=>0, 'name'=>'Administrator Uji', 'role'=>'admin'];
    $themeName = 'arunika-emerald';
    $themeConfig = $data['themes'][$themeName];
    $title = 'Pengaturan';
    $_SERVER['REQUEST_URI'] = '/admin/pengaturan';
    ob_start(); require VIEW_PATH . '/backend/settings/index.php'; $content = ob_get_clean();
    ob_start(); require VIEW_PATH . '/layouts/backend.php'; $html = ob_get_clean();
    $html = str_replace('<head>', '<head><base href="http://rahayat-cms.test/">', $html);
    file_put_contents($output . '/admin-settings.html', $html);
})();
echo 'Rendered '.(count($pages)*count(PublicTheme::NAMES))." public synthetic pages and admin settings in storage/cache/theme-preview.\n";
