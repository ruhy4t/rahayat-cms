<?php
/**
 * ============================================
 * News Controller - Frontend
 * ============================================
 */

declare(strict_types=1);

class NewsController extends Controller
{
    private News $newsModel;
    private SchoolProfile $profileModel;
    private SiteSetting $settingModel;

    public function __construct()
    {
        $this->newsModel = new News();
        $this->profileModel = new SchoolProfile();
        $this->settingModel = new SiteSetting();
    }

    /**
     * News listing
     */
    public function index(): void
    {
        $page = (int) ($this->get('page', 1));
        $category = $this->get('category');

        $baseData = [
            'profile' => $this->profileModel->getProfile(),
            'settings' => $this->settingModel->getAll(),
            'theme' => $this->settingModel->getTheme()
        ];

        if ($category) {
            $news = $this->newsModel->getByCategory($category, 20);
            $data = array_merge($baseData, [
                'title' => 'Berita - ' . ucfirst($category),
                'news' => $news,
                'category' => $category,
            ]);
        } else {
            $result = $this->newsModel->paginateWithAuthor($page);
            $data = array_merge($baseData, [
                'title' => 'Berita',
                'news' => $result['data'],
                'pagination' => $result,
            ]);
        }

        $this->view('frontend.news.index', $data, 'frontend');
    }

    /**
     * Single news article
     */
    public function show(string $slug): void
    {
        $news = $this->newsModel->findBySlug($slug);

        if (!$news || ($news['status'] ?? '') !== 'published') {
            http_response_code(404);
            $this->view('errors.404', ['title' => 'Tidak Ditemukan'], 'frontend');
            return;
        }

        // Increment view count (fail silently if column missing on hosting)
        try {
            $this->newsModel->incrementViews($news['id']);
        } catch (\Throwable $e) {
            error_log('incrementViews failed: ' . $e->getMessage());
        }

        // Get related news (handle null/empty category gracefully)
        $related = [];
        $category = $news['category'] ?? '';
        if ($category !== '') {
            try {
                $related = $this->newsModel->getByCategory($category, 4);
                $related = array_filter($related, fn($item) => $item['id'] !== $news['id']);
            } catch (\Throwable $e) {
                error_log('getByCategory failed: ' . $e->getMessage());
            }
        }

        $data = [
            'title' => $news['title'],
            'news' => $news,
            'related' => array_slice($related, 0, 3),
            'profile' => $this->profileModel->getProfile(),
            'settings' => $this->settingModel->getAll(),
            'theme' => $this->settingModel->getTheme()
        ];

        $this->view('frontend.news.show', $data, 'frontend');
    }

    /**
     * Search news
     */
    public function search(): void
    {
        $term = $this->get('q', '');
        $news = [];

        if ($term) {
            $news = $this->newsModel->searchNews($term, 20);
        }

        $data = [
            'title' => 'Cari Berita',
            'news' => $news,
            'searchTerm' => $term,
            'profile' => $this->profileModel->getProfile(),
            'settings' => $this->settingModel->getAll(),
            'theme' => $this->settingModel->getTheme()
        ];

        $this->view('frontend.news.search', $data, 'frontend');
    }
}
