<?php

namespace Tests\Unit;

use App\Support\NavMenu;
use PHPUnit\Framework\TestCase;

class NavMenuOrderTest extends TestCase
{
    /** @return list<array<string, mixed>> */
    protected function items(): array
    {
        return [['key' => 'a'], ['key' => 'b'], ['key' => 'c'], ['key' => 'd']];
    }

    /**
     * @param  list<array<string, mixed>>  $items
     * @return list<string>
     */
    protected function keys(array $items): array
    {
        return array_column($items, 'key');
    }

    public function test_default_order_without_preferences(): void
    {
        $this->assertSame(['a', 'b', 'c', 'd'], $this->keys(NavMenu::ordered($this->items(), [], [])));
    }

    public function test_unpinned_items_sort_by_click_count(): void
    {
        $ordered = NavMenu::ordered($this->items(), [], ['d' => 9, 'b' => 4]);

        $this->assertSame(['d', 'b', 'a', 'c'], $this->keys($ordered));
    }

    public function test_equal_click_counts_fall_back_to_the_default_order(): void
    {
        $ordered = NavMenu::ordered($this->items(), [], ['d' => 5, 'b' => 5]);

        $this->assertSame(['b', 'd', 'a', 'c'], $this->keys($ordered));
    }

    public function test_pinned_item_holds_its_slot_while_the_rest_flow_by_usage(): void
    {
        $ordered = NavMenu::ordered($this->items(), ['a' => 2], ['d' => 9, 'c' => 4]);

        $this->assertSame(['d', 'c', 'a', 'b'], $this->keys($ordered));
        $this->assertTrue($ordered[2]['pinned']);
        $this->assertFalse($ordered[0]['pinned']);
    }

    public function test_pins_for_missing_items_are_ignored_and_out_of_range_slots_clamp(): void
    {
        $ordered = NavMenu::ordered($this->items(), ['gone' => 0, 'a' => 99], []);

        $this->assertSame(['b', 'c', 'd', 'a'], $this->keys($ordered));
    }

    public function test_two_items_pinned_to_one_slot_do_not_collide(): void
    {
        $ordered = NavMenu::ordered($this->items(), ['c' => 1, 'd' => 1], []);

        $this->assertSame(['a', 'c', 'd', 'b'], $this->keys($ordered));
    }

    public function test_every_item_pinned_keeps_all_slots(): void
    {
        $ordered = NavMenu::ordered($this->items(), ['d' => 0, 'c' => 1, 'b' => 2, 'a' => 3], ['a' => 99]);

        $this->assertSame(['d', 'c', 'b', 'a'], $this->keys($ordered));
    }

    public function test_empty_menu_is_handled(): void
    {
        $this->assertSame([], NavMenu::ordered([], ['a' => 0], ['a' => 1]));
    }
}
