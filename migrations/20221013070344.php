<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class V20221013070344 extends AbstractMigration
{
    private int $id = 63;

    private string $message = <<<EOT
Тред поддержки и разработки серверной части этого чана.

Репозиторий тут - https://github.com/U-Me-Chan/pissykaka

Документация по API - https://github.com/U-Me-Chan/pissykaka/wiki

Features:
[x] Механизм сажи
[x] Лента последних постов
[x] Удаление постов пользователями
[x] Удаление постов администратором
[x] Показывать на странице доски треды с несколькими последними постами
[x] Подпись поста трипкодом/GPG-ключом/etc
[x] Тред перестаёт бампаться после 500 постов
[x] Конъюнкция досок при запросе вида /v2/board/b+t
[x] Обработка медиа-ссылок в теле поста
[-] Загрузка и прикрепление файлов к постам(уже есть filestore.scheoble.xyz, пусть разработчики фронтендов интегрируются)
[ ] Интерфейс модератора доски/чана
[ ] Капча с возможностью точечного включения на уровне треда/доски/чана
[ ] Поиск по постам

Т.к. тред является мод-тредом, то списки будут редактироваться по мере выполнения.
EOT;

    public function up(): void
    {
        $this->execute("UPDATE posts SET message = '{$this->message}' WHERE id = {$this->id}");
    }

    public function down(): void
    {
    }
}
