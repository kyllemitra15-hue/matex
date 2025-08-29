Database setup for MATEX website

Quick steps (XAMPP on Windows):

1) Using phpMyAdmin
   - Open http://localhost/phpmyadmin
   - Click Import, choose `db_init.sql` from this project, and run it.

2) Or run the helper PHP script
   - Place this project in your XAMPP `htdocs` (already present).
   - In your browser open: http://localhost/matex/create_databases.php
   - The script will create `contact_db.messages` and `newsletter_db.subscribers`.

Notes:
- Default MySQL user is `root` with no password on XAMPP. If you changed it, edit `create_databases.php` to set the correct credentials.
- After running, remove `create_databases.php` for security.
- `submit.php` and `subscribe.php` expect the databases/tables created by `db_init.sql`.

If you want, I can also add a simple admin viewer to list saved messages.
