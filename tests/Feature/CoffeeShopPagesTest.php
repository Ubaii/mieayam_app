<?php

namespace Tests\Feature;

use App\Models\CafeTable;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoffeeShopPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_guest_is_redirected_to_login_and_protected_pages_require_authentication(): void
    {
        $this->get('/')->assertRedirect('/login');
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/cashier')->assertRedirect('/login');
        $this->get('/menus')->assertRedirect('/login');
    }

    public function test_first_admin_can_register_once_and_login_page_has_password_reset_link(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Tampilkan password')
            ->assertSee('Lupa password?')
            ->assertDontSee('Kembali')
            ->assertSee('Buat akun administrator');

        $this->get('/register')
            ->assertOk()
            ->assertSee('Buat akun administrator')
            ->assertSee('name="name"', false)
            ->assertSee('name="email"', false)
            ->assertSee('name="password_confirmation"', false)
            ->assertSee('Buat akun dan masuk');

        $this->post('/register', [
            'name' => 'Admin Senja',
            'email' => 'admin@kopisenja.test',
            'password' => 'kopisenja123',
            'password_confirmation' => 'kopisenja123',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'admin@kopisenja.test', 'role' => 'admin']);
        $this->get('/register')->assertRedirect('/dashboard');

        $rememberToken = User::query()->firstOrFail()->remember_token;
        $this->post('/logout')
            ->assertRedirect('/login')
            ->assertSessionHas('status', 'Anda sudah keluar. Masukkan kembali email dan password untuk masuk.');
        $this->assertGuest();
        $this->assertNotSame($rememberToken, User::query()->firstOrFail()->remember_token);
        $this->assertDatabaseHas('users', ['email' => 'admin@kopisenja.test']);
        $this->get('/login')
            ->assertSee('Anda sudah keluar. Masukkan kembali email dan password untuk masuk.')
            ->assertDontSee('Buat akun administrator');
        $this->get('/cashier')->assertRedirect('/login');

        $this->from('/login')->post('/login', [
            'email' => 'admin@kopisenja.test',
            'password' => 'wrong-password',
        ])->assertRedirect('/login')->assertSessionHasErrors('email');

        $this->post('/login', [
            'email' => 'ADMIN@KOPISENJA.TEST',
            'password' => 'kopisenja123',
            'remember' => '1',
        ])->assertRedirect('/cashier');

        $this->assertAuthenticated();
    }

    public function test_login_explains_that_cashier_accounts_are_created_by_an_admin(): void
    {
        User::factory()->create(['role' => 'admin']);

        $this->get('/login')
            ->assertOk()
            ->assertSee('masuk sebagai administrator')
            ->assertSee('administrator atau kasir')
            ->assertDontSee('Buat akun administrator');
    }

    public function test_password_recovery_pages_share_the_login_layout(): void
    {
        $this->get('/forgot-password')
            ->assertSee('class="login-layout"', false)
            ->assertSee('class="login-visual"', false)
            ->assertSee('Kirim tautan reset');

        $this->get('/reset-password/sample-token?email=admin%40kopisenja.test')
            ->assertSee('class="login-layout"', false)
            ->assertSee('class="login-visual"', false)
            ->assertSee('Simpan password baru')
            ->assertSee('data-password-toggle', false);
    }

    public function test_all_application_pages_render_for_an_authenticated_user(): void
    {
        $this->actingAs(User::factory()->create());

        foreach ([
            '/dashboard',
            '/cashier',
            '/menus',
            '/categories',
            '/tables',
            '/table-status',
            '/transactions',
            '/reports',
            '/receipt',
            '/users',
        ] as $path) {
            $this->get($path)
                ->assertOk()
                ->assertSee('MIE AYAM WENGI\'57')
                ->assertSee('Kembali');
        }
    }

    public function test_empty_menu_state_is_rendered_outside_the_scrollable_menu_table(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/menus')
            ->assertOk()
            ->assertSee('Belum ada menu')
            ->assertSee('Gunakan tombol di bawah untuk mulai menambahkan menu MIE AYAM WENGI\'57.')
            ->assertSee('data-modal-open="add-menu"', false);
    }

    public function test_admin_can_create_cashier_and_administrator_accounts(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $this->get('/users')
            ->assertOk()
            ->assertSee('Akun Pegawai')
            ->assertSee('name="role"', false)
            ->assertSee('Administrator');

        $this->post('/users', [
            'name' => 'Kasir Senja',
            'email' => 'KASIR@KOPISENJA.TEST',
            'password' => 'pegawai123',
            'password_confirmation' => 'pegawai123',
            'role' => 'cashier',
        ])->assertRedirect('/users');

        $this->assertDatabaseHas('users', [
            'name' => 'Kasir Senja',
            'email' => 'kasir@kopisenja.test',
            'role' => 'cashier',
        ]);

        $this->post('/users', [
            'name' => 'Admin Kedua',
            'email' => 'ADMIN2@KOPISENJA.TEST',
            'password' => 'pegawai123',
            'password_confirmation' => 'pegawai123',
            'role' => 'admin',
        ])->assertRedirect('/users')
            ->assertSessionHas('status', 'Akun administrator berhasil dibuat.');

        $this->assertDatabaseHas('users', [
            'name' => 'Admin Kedua',
            'email' => 'admin2@kopisenja.test',
            'role' => 'admin',
        ]);
        $admin2 = User::query()->where('email', 'admin2@kopisenja.test')->firstOrFail();

        $this->from('/users')->post('/users', [
            'name' => 'Peran Tidak Valid',
            'email' => 'invalid-role@kopisenja.test',
            'password' => 'pegawai123',
            'password_confirmation' => 'pegawai123',
            'role' => 'owner',
        ])->assertRedirect('/users')->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', ['email' => 'invalid-role@kopisenja.test']);

        $this->delete('/users/'.$admin2->id)->assertRedirect('/users');
        $this->assertDatabaseMissing('users', ['id' => $admin2->id]);

        $this->post('/logout')->assertRedirect('/login');
        $this->post('/login', [
            'email' => 'kasir@kopisenja.test',
            'password' => 'pegawai123',
        ])->assertRedirect('/cashier');
    }

    public function test_cashier_can_only_use_cashier_pages_and_own_receipts(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $this->actingAs($cashier);

        $this->get('/')->assertRedirect('/cashier');
        $this->get('/cashier')
            ->assertOk()
            ->assertSee('Kasir')
            ->assertDontSee('Manajemen');
        $this->get('/dashboard')->assertForbidden();
        $this->get('/users')->assertForbidden();
        $this->get('/menus')->assertForbidden();
        $this->post('/users', [
            'name' => 'Tidak Boleh Menambah Akun',
            'email' => 'blocked@kopisenja.test',
            'password' => 'pegawai123',
            'password_confirmation' => 'pegawai123',
            'role' => 'admin',
        ])->assertForbidden();
        $this->assertDatabaseMissing('users', ['email' => 'blocked@kopisenja.test']);

        $category = Category::query()->create(['name' => 'Coffee']);
        $menu = Menu::query()->create(['category_id' => $category->id, 'name' => 'Espresso', 'price' => 18000]);
        $this->post('/cashier/checkout', [
            'payment_method' => 'Tunai',
            'amount_paid' => 18000,
            'items' => [['menu_id' => $menu->id, 'quantity' => 1]],
        ])->assertRedirect();
        $transaction = Transaction::query()->firstOrFail();

        $this->get("/transactions/{$transaction->id}/receipt")->assertOk()->assertSee($transaction->invoice);
        $this->actingAs(User::factory()->create(['role' => 'cashier']))
            ->get("/transactions/{$transaction->id}/receipt")
            ->assertForbidden();
    }

    public function test_admin_can_delete_a_cashier_without_transactions(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cashier = User::factory()->create(['role' => 'cashier']);
        $this->actingAs($admin);

        $this->get('/users')
            ->assertOk()
            ->assertSee('aria-label="Hapus akun '.$cashier->name.'"', false);

        $this->delete('/users/'.$cashier->id)
            ->assertRedirect('/users')
            ->assertSessionHas('status', 'Akun berhasil dihapus.');

        $this->assertDatabaseMissing('users', ['id' => $cashier->id]);
    }

    public function test_admin_cannot_delete_their_own_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $this->get('/users')
            ->assertSeeText('Akun aktif & administrator terakhir');

        $this->delete('/users/'.$admin->id)
            ->assertRedirect('/users')
            ->assertSessionHasErrors(['user' => 'Akun yang sedang digunakan tidak dapat dihapus.']);
        $this->assertModelExists($admin);

        $otherAdmin = User::factory()->create(['role' => 'admin']);
        $this->get('/users')
            ->assertSee('Akun yang sedang digunakan')
            ->assertSee('aria-label="Hapus akun '.$otherAdmin->name.'"', false);

        $this->delete('/users/'.$otherAdmin->id)
            ->assertRedirect('/users')
            ->assertSessionHas('status', 'Akun berhasil dihapus.');
        $this->assertDatabaseMissing('users', ['id' => $otherAdmin->id]);
        $this->assertModelExists($admin);
    }

    public function test_accounts_with_transactions_cannot_be_deleted(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cashier = User::factory()->create(['role' => 'cashier']);
        $otherAdmin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $category = Category::query()->create(['name' => 'Coffee']);
        $menu = Menu::query()->create(['category_id' => $category->id, 'name' => 'Espresso', 'price' => 18000]);

        $checkout = [
            'payment_method' => 'Tunai',
            'amount_paid' => 18000,
            'items' => [['menu_id' => $menu->id, 'quantity' => 1]],
        ];

        $this->actingAs($cashier)->post('/cashier/checkout', $checkout);
        $cashierTransaction = Transaction::query()->where('user_id', $cashier->id)->firstOrFail();

        $this->actingAs($otherAdmin)->post('/cashier/checkout', $checkout);
        $adminTransaction = Transaction::query()->where('user_id', $otherAdmin->id)->firstOrFail();

        $this->actingAs($admin);
        $this->get('/users')
            ->assertSee('Ada riwayat transaksi');

        $this->delete('/users/'.$cashier->id)
            ->assertRedirect('/users')
            ->assertSessionHasErrors(['user' => 'Akun dengan riwayat transaksi tidak dapat dihapus agar catatan transaksi tetap tersimpan.']);
        $this->assertModelExists($cashier);

        $this->delete('/users/'.$otherAdmin->id)
            ->assertRedirect('/users')
            ->assertSessionHasErrors(['user' => 'Akun dengan riwayat transaksi tidak dapat dihapus agar catatan transaksi tetap tersimpan.']);
        $this->assertModelExists($otherAdmin);
        $this->assertDatabaseHas('transactions', ['id' => $cashierTransaction->id]);
        $this->assertDatabaseHas('transactions', ['id' => $adminTransaction->id]);
    }

    public function test_category_menu_and_table_controls_persist_changes(): void
    {
        $this->actingAs(User::factory()->create());

        $this->from('/categories')->post('/categories', ['name' => 'Coffee', 'description' => 'Kopi pilihan'])
            ->assertRedirect('/categories');
        $category = Category::query()->firstOrFail();

        $this->put("/categories/{$category->id}", [
            'name' => 'Signature Coffee',
            'description' => 'Racikan khas',
            'is_active' => 1,
        ])->assertRedirect('/categories');

        $this->from('/menus')->post('/menus', [
            'name' => 'Cafe Latte',
            'description' => 'Espresso dan susu',
            'category_id' => $category->id,
            'price' => 30000,
        ])->assertRedirect('/menus');
        $menu = Menu::query()->firstOrFail();
        $this->assertDatabaseHas('menus', ['id' => $menu->id, 'name' => 'Cafe Latte', 'price' => 30000, 'is_active' => 1]);
        $this->patch("/menus/{$menu->id}/status")->assertRedirect('/menus');
        $this->assertDatabaseHas('menus', ['id' => $menu->id, 'is_active' => 0]);

        $this->put("/menus/{$menu->id}", [
            'name' => 'Latte',
            'description' => 'Kopi susu',
            'category_id' => $category->id,
            'price' => 32000,
            'is_active' => 1,
        ])->assertRedirect('/menus');

        $this->from('/tables')->post('/tables', ['table_number' => 'A01', 'capacity' => 2, 'location' => 'Depan'])
            ->assertRedirect('/tables');
        $table = CafeTable::query()->firstOrFail();
        $this->get('/tables')
            ->assertSee(route('tables.active.toggle', $table), false)
            ->assertSee('name="_method" value="patch"', false);

        $this->put("/tables/{$table->id}", [
            'table_number' => 'A01',
            'capacity' => 4,
            'location' => 'Teras',
            'status' => 'reserved',
            'is_active' => 1,
        ])->assertRedirect('/tables');
        $this->assertDatabaseHas('cafe_tables', ['id' => $table->id, 'capacity' => 4, 'status' => 'reserved']);
        $this->patch("/tables/{$table->id}/status", ['status' => 'available'])->assertRedirect();
        $this->assertDatabaseHas('cafe_tables', ['id' => $table->id, 'status' => 'available']);

        $this->patch("/tables/{$table->id}/active")->assertRedirect('/tables');
        $this->assertDatabaseHas('cafe_tables', ['id' => $table->id, 'is_active' => 0]);
        $this->get('/tables')->assertSee('aria-checked="false"', false);
        $this->patch("/tables/{$table->id}/active")->assertRedirect('/tables');
        $this->assertDatabaseHas('cafe_tables', ['id' => $table->id, 'is_active' => 1]);
        $this->get('/tables')->assertSee('aria-checked="true"', false);

        $this->from('/menus')->delete("/menus/{$menu->id}")->assertRedirect('/menus');
        $this->from('/categories')->delete("/categories/{$category->id}")->assertRedirect('/categories');
        $this->from('/tables')->delete("/tables/{$table->id}")->assertRedirect('/tables');
    }

    public function test_database_seeder_adds_food_and_drink_categories_without_duplicates(): void
    {
        $this->actingAs(User::factory()->create());
        $this->seed();
        $this->seed();

        $this->assertDatabaseHas('categories', ['name' => 'Makanan', 'is_active' => 1]);
        $this->assertDatabaseHas('categories', ['name' => 'Minuman', 'is_active' => 1]);
        $this->assertSame(2, Category::query()->whereIn('name', ['Makanan', 'Minuman'])->count());
        $this->get('/categories')->assertSeeText('Makanan')->assertSeeText('Minuman');
    }

    public function test_menu_can_be_set_as_temporarily_unavailable_and_activated_again(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        $category = Category::query()->create(['name' => 'Makanan']);

        $this->from('/menus')->post('/menus', [
            'name' => 'Nasi Goreng Kosong',
            'category_id' => $category->id,
            'price' => 25000,
            'is_active' => 0,
        ])->assertRedirect('/menus');

        $menu = Menu::query()->where('name', 'Nasi Goreng Kosong')->firstOrFail();
        $this->assertDatabaseHas('menus', ['id' => $menu->id, 'is_active' => 0]);
        $this->get('/menus')
            ->assertSee('Nonaktif — kosong sementara')
            ->assertSee('Kosong sementara')
            ->assertSee('Aktifkan kembali');
        $this->get('/cashier')->assertDontSee('Nasi Goreng Kosong');

        $this->from('/menus')->patch("/menus/{$menu->id}/status")
            ->assertRedirect('/menus')
            ->assertSessionHas('status', 'Menu berhasil diaktifkan kembali.');
        $this->assertDatabaseHas('menus', ['id' => $menu->id, 'is_active' => 1]);
        $this->get('/cashier')->assertSee('Nasi Goreng Kosong');
    }

    public function test_cashier_saves_a_transaction_updates_table_and_exposes_its_receipt(): void
    {
        $this->actingAs(User::factory()->create());
        $category = Category::query()->create(['name' => 'Coffee']);
        $menu = Menu::query()->create([
            'category_id' => $category->id,
            'name' => 'Espresso',
            'price' => 18000,
        ]);
        $table = CafeTable::query()->create([
            'table_number' => 'A01',
            'capacity' => 2,
            'location' => 'Area depan',
        ]);

        $this->post('/cashier/checkout', [
            'table_id' => $table->id,
            'payment_method' => 'Tunai',
            'amount_paid' => 40000,
            'items' => [
                ['menu_id' => $menu->id, 'quantity' => 2, 'note' => 'Less sugar'],
            ],
        ])->assertRedirect();

        $transaction = Transaction::query()->with('items')->firstOrFail();
        $this->assertSame(36000, $transaction->total);
        $this->assertSame(4000, $transaction->change_amount);
        $this->assertSame('Less sugar', $transaction->items->first()->note);
        $this->assertDatabaseHas('cafe_tables', ['id' => $table->id, 'status' => 'occupied']);
        $this->get("/transactions/{$transaction->id}/receipt")
            ->assertOk()
            ->assertSee($transaction->invoice)
            ->assertSee('Rp 36.000')
            ->assertSee('Kembalian')
            ->assertSee('Rp 4.000');
        $this->get('/reports')
            ->assertOk()
            ->assertSee('Rp 36.000');
    }

    public function test_invalid_cashier_amount_does_not_create_a_transaction(): void
    {
        $this->actingAs(User::factory()->create());
        $category = Category::query()->create(['name' => 'Coffee']);
        $menu = Menu::query()->create(['category_id' => $category->id, 'name' => 'Espresso', 'price' => 18000]);

        $this->from('/cashier')->post('/cashier/checkout', [
            'payment_method' => 'Tunai',
            'amount_paid' => 10000,
            'items' => [['menu_id' => $menu->id, 'quantity' => 1]],
        ])->assertRedirect('/cashier')->assertSessionHasErrors('amount_paid');

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_non_cash_payment_records_the_exact_total_without_change(): void
    {
        $this->actingAs(User::factory()->create());
        $category = Category::query()->create(['name' => 'Coffee']);
        $menu = Menu::query()->create(['category_id' => $category->id, 'name' => 'Espresso', 'price' => 18000]);

        $this->post('/cashier/checkout', [
            'payment_method' => 'QRIS',
            'amount_paid' => 0,
            'items' => [['menu_id' => $menu->id, 'quantity' => 1]],
        ])->assertRedirect();

        $transaction = Transaction::query()->firstOrFail();
        $this->assertSame(18000, $transaction->amount_paid);
        $this->assertSame(0, $transaction->change_amount);
    }
}
