<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Delete Account
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Once your account is deleted, all of its resources and data will be permanently deleted.
            Before deleting your account, please download any data or information that you wish to retain.
        </p>
    </header>

    <!-- Delete Account Button -->
    <button type="button"
        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
        onclick="document.getElementById('delete-modal').classList.remove('hidden');">
        Delete Account
    </button>

    <!-- Confirm Delete Modal -->
    <div id="delete-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg max-w-lg w-full">
            <form method="post" action="/profile" class="space-y-4">
                <!-- CSRF Token -->
                <input type="hidden" name="_token" value="CSRF_TOKEN_HERE">
                <!-- DELETE Method -->
                <input type="hidden" name="_method" value="DELETE">

                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Are you sure you want to delete your account?
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Once your account is deleted, all of its resources and data will be permanently deleted.
                    Please enter your password to confirm you would like to permanently delete your account.
                </p>

                <!-- Password input -->
                <div class="mt-4">
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password"
                        class="mt-1 block w-3/4 border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
                        placeholder="Password">
                    <!-- Error messages placeholder -->
                    <!-- <p class="text-sm text-red-600">Incorrect password.</p> -->
                </div>

                <!-- Modal buttons -->
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400"
                        onclick="document.getElementById('delete-modal').classList.add('hidden');">
                        Cancel
                    </button>

                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Delete Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Optional: small script to toggle modal -->
<script>
    // Close modal on background click
    document.getElementById('delete-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });
</script>
