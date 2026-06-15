<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Core\Database;
use App\Core\Validator;
use App\Models\Category;
use App\Models\Task;
use App\Models\User;

putenv('DB_PATH=:memory:');
require dirname(__DIR__) . '/bootstrap.php';

Database::migrate();

$passed = 0;
$failed = 0;

function test(string $name, callable $callback): void
{
    global $passed, $failed;

    try {
        $callback();
        $passed++;
        echo "[PASS] {$name}\n";
    } catch (Throwable $exception) {
        $failed++;
        echo "[FAIL] {$name}: {$exception->getMessage()}\n";
    }
}

function assertTrue(bool $condition, string $message = 'Assertion failed.'): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

function assertSameValue(mixed $expected, mixed $actual, string $message = ''): void
{
    if ($expected !== $actual) {
        $detail = $message !== '' ? $message : 'Values are not identical.';
        throw new RuntimeException($detail . ' Expected ' . var_export($expected, true) . ', got ' . var_export($actual, true) . '.');
    }
}

test('registration validation rejects invalid input', function (): void {
    $errors = Validator::registration([
        'name' => 'A',
        'email' => 'not-an-email',
        'password' => 'short',
        'password_confirmation' => 'different',
    ]);

    assertTrue(isset($errors['name'], $errors['email'], $errors['password'], $errors['password_confirmation']));
});

test('task validation accepts a valid task', function (): void {
    $errors = Validator::task([
        'title' => 'Prepare demonstration',
        'description' => 'Show the MVC flow.',
        'due_date' => '2026-06-21',
        'status' => 'pending',
    ]);

    assertSameValue([], $errors);
});

test('CSRF token is stable and validated', function (): void {
    unset($_SESSION['_token']);
    $token = Csrf::token();

    assertTrue(strlen($token) === 64);
    assertTrue(Csrf::validate($token));
    assertTrue(!Csrf::validate('invalid'));
});

$aliceId = 0;
$bobId = 0;
$categoryId = 0;
$taskId = 0;

test('users are created with hashed passwords', function () use (&$aliceId, &$bobId): void {
    $aliceId = User::create('Alice', 'alice@example.com', 'password123');
    $bobId = User::create('Bob', 'bob@example.com', 'password456');
    $alice = User::findByEmail('ALICE@example.com');

    assertTrue($alice !== null);
    assertSameValue($aliceId, (int) $alice['id']);
    assertTrue(password_verify('password123', $alice['password']));
});

test('category and task CRUD uses their relationship', function () use (&$aliceId, &$categoryId, &$taskId): void {
    $categoryId = Category::create($aliceId, 'Course');
    $taskId = Task::create($aliceId, [
        'category_id' => $categoryId,
        'title' => 'Finish project',
        'description' => 'Complete the required features.',
        'due_date' => '2026-06-21',
        'status' => 'pending',
    ]);

    $task = Task::findOwned($taskId, $aliceId);
    assertTrue($task !== null);
    assertSameValue('Course', $task['category_name']);

    Task::update($taskId, $aliceId, [
        'category_id' => $categoryId,
        'title' => 'Finish MVC project',
        'description' => 'Complete and test the required features.',
        'due_date' => '2026-06-21',
        'status' => 'completed',
    ]);

    $updated = Task::findOwned($taskId, $aliceId);
    assertSameValue('Finish MVC project', $updated['title']);
    assertSameValue('completed', $updated['status']);
    assertSameValue(['total' => 1, 'pending' => 0, 'completed' => 1], Task::stats($aliceId));
});

test('ownership checks prevent cross-user access', function () use (&$bobId, &$categoryId, &$taskId): void {
    assertSameValue(null, Category::findOwned($categoryId, $bobId));
    assertSameValue(null, Task::findOwned($taskId, $bobId));
    assertTrue(!Task::delete($taskId, $bobId));
});

test('deleting a category keeps tasks and clears the relationship', function () use (&$aliceId, &$categoryId, &$taskId): void {
    assertTrue(Category::delete($categoryId, $aliceId));
    $task = Task::findOwned($taskId, $aliceId);

    assertTrue($task !== null);
    assertSameValue(null, $task['category_id']);
    assertSameValue(null, $task['category_name']);
});

test('task deletion completes CRUD lifecycle', function () use (&$aliceId, &$taskId): void {
    assertTrue(Task::delete($taskId, $aliceId));
    assertSameValue(null, Task::findOwned($taskId, $aliceId));
});

echo "\n{$passed} passed, {$failed} failed.\n";
exit($failed === 0 ? 0 : 1);
