<?php
declare(strict_types=1);
require __DIR__.'/security-bootstrap.php';
$base='http://rahayat-cms.test';
if (!str_ends_with((string)parse_url($base,PHP_URL_HOST),'.test')) { throw new RuntimeException('Local test host required'); }
$db=Database::getInstance();
$backup=$db->fetch('SELECT * FROM site_settings WHERE setting_key = ?', ['theme']);
$model=new SiteSetting(); $checks=0;
try {
    foreach (PublicTheme::NAMES as $theme) {
        $model->set('theme',$theme);
        foreach (['/','/profil','/berita','/galeri','/profil/gtk','/kontak','/agenda','/kalender-pendidikan','/prestasi','/testimoni','/alumni'] as $path) {
            $curl=curl_init($base.$path);
            curl_setopt_array($curl,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>15,CURLOPT_USERAGENT=>'ThemeTestBot/1.0']);
            $html=curl_exec($curl);$status=curl_getinfo($curl,CURLINFO_RESPONSE_CODE);unset($curl);
            if ($status!==200 || !is_string($html) || !str_contains($html,'theme-'.$theme) || preg_match('/(?:Fatal error|Warning:|Deprecated:)/',$html)) { throw new RuntimeException('Theme render failed: '.$theme.' '.$path.' HTTP '.$status); }
            if (!str_contains($html,'next-header') || !str_contains($html,'next-footer')) { throw new RuntimeException('Theme chrome missing'); }
            $checks++;
        }
    }
} finally {
    if ($backup) { $db->update('site_settings',['setting_value'=>$backup['setting_value'],'updated_at'=>$backup['updated_at']],'setting_key = ?',['theme']); }
    else { $db->delete('site_settings','setting_key = ?',['theme']); }
}
echo "PASS: {$checks} local HTTP pages across four new themes; original theme restored.\n";
