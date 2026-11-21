<?php
declare(strict_types=1);

namespace App\Http;

use App\Models\BookFactory;
use App\Models\Loan;
use App\Models\Member;
use App\Repositories\JsonStore;
use App\Services\CatalogService;
use App\Services\LoanService;
use App\Services\MemberService;
use DomainException;
use InvalidArgumentException;
use Throwable;

final class Application
{
    private JsonStore $store;
    private CatalogService $catalog;
    private MemberService $members;
    private LoanService $loans;
    private const PAGES = ['dashboard', 'catalog', 'members', 'transactions'];

    public function __construct(string $file)
    {
        $this->store = new JsonStore($file);
        $this->catalog = new CatalogService($this->store);
        $this->members = new MemberService($this->store);
        $this->loans = new LoanService($this->store);
    }

    public function run(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $this->handlePost();
            return;
        }
        $this->render();
    }

    private function handlePost(): void
    {
        $page = $this->page($_POST['return_page'] ?? 'dashboard');
        try {
            $submitted = $_POST['csrf_token'] ?? null;
            if (!is_string($submitted) || !hash_equals((string) $_SESSION['csrf_token'], $submitted)) {
                throw new DomainException('Sesi formulir tidak valid. Muat ulang halaman dan coba lagi.');
            }
            $action = $_POST['action'] ?? '';
            if (!is_string($action)) {
                throw new InvalidArgumentException('Aksi tidak valid.');
            }
            switch ($action) {
                case 'book.save': $this->catalog->save($_POST); $message = 'Data e-book berhasil disimpan.'; break;
                case 'book.delete': $this->catalog->delete($_POST); $message = 'E-book berhasil dihapus.'; break;
                case 'member.save': $this->members->save($_POST); $message = 'Data anggota berhasil disimpan.'; break;
                case 'member.delete': $this->members->delete($_POST); $message = 'Anggota berhasil dihapus.'; break;
                case 'loan.borrow': $this->loans->borrow($_POST); $message = 'Peminjaman berhasil. Tenggat tercatat otomatis sesuai jenis e-book.'; break;
                case 'loan.return': $this->loans->returnBook($_POST); $message = 'E-book berhasil dikembalikan.'; break;
                default: throw new InvalidArgumentException('Aksi tidak dikenal.');
            }
            $_SESSION['flash'] = ['type' => 'success', 'message' => $message];
        } catch (DomainException | InvalidArgumentException $e) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => $e->getMessage()];
        } catch (Throwable $e) {
            error_log((string) $e);
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Terjadi kendala pada server. Periksa log PHP dan folder storage.'];
        }
        header('Location: ?page=' . urlencode($page), true, 303);
        exit;
    }

    private function page(mixed $value): string
    {
        return is_string($value) && in_array($value, self::PAGES, true) ? $value : 'dashboard';
    }

    private function render(): void
    {
        $page = $this->page($_GET['page'] ?? 'dashboard');
        $state = $this->store->read();
        $books = array_map([BookFactory::class, 'fromArray'], $state['books']);
        $members = array_map([Member::class, 'fromArray'], $state['members']);
        $loans = array_reverse(array_map([Loan::class, 'fromArray'], $state['loans']));
        $activeBookIds = [];
        $activeCount = 0;
        $returnedCount = 0;
        foreach ($loans as $loan) {
            if ($loan->isActive()) {
                $activeBookIds[$loan->getBookId()] = true;
                $activeCount++;
            } else {
                $returnedCount++;
            }
        }
        $summary = [
            'books' => count($books), 'members' => count($members),
            'active' => $activeCount, 'returned' => $returnedCount,
            'available' => count($books) - count($activeBookIds),
        ];
        $categories = CatalogService::CATEGORIES;
        $accents = CatalogService::ACCENTS;
        $csrf = $_SESSION['csrf_token'];
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        require dirname(__DIR__, 2) . '/templates/layout.php';
    }
}
