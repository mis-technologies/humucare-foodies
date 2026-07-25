#!/bin/bash

# Cleanup script to run on production BEFORE deploying queue worker fixes
# This clears old broken jobs and prepares the database

echo "🧹 Cleaning up production database for queue worker deployment..."

# Clear all pending jobs from imports queue
php artisan queue:clear database --queue=imports

# Clear all failed jobs
php artisan queue:flush

# Cancel any stuck processing imports
php artisan tinker --execute="
\DB::table('product_imports')
    ->where('status', 'processing')
    ->update([
        'status' => 'cancelled',
        'completed_at' => now()
    ]);
echo 'Cancelled stuck imports' . PHP_EOL;
"

echo "✅ Database cleanup complete!"
echo ""
echo "Next steps:"
echo "1. Deploy your code changes"
echo "2. Run 'supervisorctl update' to reload queue workers"
echo "3. Test with a new import"
