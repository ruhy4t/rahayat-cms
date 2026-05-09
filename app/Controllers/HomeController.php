<?php
/**
 * ============================================
 * Home Controller - Frontend
 * ============================================
 */

declare(strict_types=1);

class HomeController extends Controller
{
    private News $newsModel;
    private SchoolProfile $profileModel;
    private HeroSlide $slideModel;
    private SiteSetting $settingModel;
    private GalleryAlbum $albumModel;
    private GalleryItem $itemModel;
    private Facility $facilityModel;
    private Staff $staffModel;
    private Ekstrakurikuler $ekskulModel;

    public function __construct()
    {
        $this->newsModel = new News();
        $this->profileModel = new SchoolProfile();
        $this->slideModel = new HeroSlide();
        $this->settingModel = new SiteSetting();
        $this->albumModel = new GalleryAlbum();
        $this->itemModel = new GalleryItem();
        $this->facilityModel = new Facility();
        $this->staffModel = new Staff();
        $this->ekskulModel = new Ekstrakurikuler();
    }

    /**
     * Homepage
     */
    public function index(): void
    {
        $profile = $this->safeValue(fn () => $this->profileModel->getProfile(), []);

        $data = [
            'title' => 'Beranda',
            'news' => $this->safeValue(fn () => $this->newsModel->getRecent(6), []),
            'profile' => $profile,
            'slides' => $this->safeValue(fn () => $this->slideModel->getActive(), []),
            'settings' => $this->safeValue(fn () => $this->settingModel->getAll(), []),
            'theme' => $this->safeValue(fn () => $this->settingModel->getTheme(), 'indigo-modern'),
            'facilities' => $this->safeValue(fn () => $this->facilityModel->getActive(), []),
            'ekskul' => $this->safeValue(fn () => $this->ekskulModel->getActive(), []),
            'flash' => $this->getFlash()
        ];

        $this->view('frontend.home', $data, 'frontend');
    }

    /**
     * School Profile page
     */
    public function profile(): void
    {
        $profile = $this->safeValue(fn () => $this->profileModel->getProfile(), []);

        $data = [
            'title' => 'Profil Sekolah',
            'profile' => $profile,
            'settings' => $this->safeValue(fn () => $this->settingModel->getAll(), []),
            'facilities' => $this->safeValue(fn () => $this->facilityModel->getActive(), [])
        ];

        $this->view('frontend.profile', $data, 'frontend');
    }

    /**
     * GTK page
     */
    public function gtk(): void
    {
        $data = [
            'title' => 'Guru & Tenaga Kependidikan',
            'profile' => $this->profileModel->getProfile(),
            'settings' => $this->settingModel->getAll(),
            'groupedStaff' => $this->staffModel->getGrouped(),
            'enableContentProtection' => true
        ];

        $this->view('frontend.gtk', $data, 'frontend');
    }

    /**
     * Gallery page
     */
    public function gallery(): void
    {
        $data = [
            'title' => 'Galeri',
            'profile' => $this->profileModel->getProfile(),
            'settings' => $this->settingModel->getAll(),
            'albums' => $this->albumModel->getWithItemCount()
        ];

        $this->view('frontend.gallery', $data, 'frontend');
    }

    /**
     * Gallery album detail
     */
    public function galleryAlbum(string $slug): void
    {
        $album = $this->albumModel->findBySlug($slug);

        if (!$album) {
            $this->redirect('/galeri');
            return;
        }

        $data = [
            'title' => 'Galeri - ' . $album['title'],
            'profile' => $this->profileModel->getProfile(),
            'settings' => $this->settingModel->getAll(),
            'album' => $album,
            'items' => $this->itemModel->getByAlbum($album['id'])
        ];

        $this->view('frontend.gallery-detail', $data, 'frontend');
    }

    /**
     * Contact page
     */
    public function contact(): void
    {
        $data = [
            'title' => 'Kontak',
            'profile' => $this->safeValue(fn () => $this->profileModel->getProfile(), []),
            'settings' => $this->safeValue(fn () => $this->settingModel->getAll(), []),
            'flash' => $this->getFlash()
        ];

        $this->view('frontend.contact', $data, 'frontend');
    }

    private function safeValue(callable $callback, mixed $fallback): mixed
    {
        try {
            return $callback();
        } catch (\Throwable $e) {
            error_log('Homepage data load failed: ' . $e->getMessage());
            return $fallback;
        }
    }
}
