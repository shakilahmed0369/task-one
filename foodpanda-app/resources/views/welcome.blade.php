<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Foodpanda App') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#FDFDFC] text-[#1b1b18] min-h-screen flex flex-col">
    <!-- Navigation -->
    <nav class="bg-[#FDFDFC] border-b border-[#e3e3e0] py-3">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <a href="/" class="font-medium text-lg">Foodpanda App</a>
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="px-4 py-2 bg-[#1b1b18] text-white rounded text-sm hover:bg-black transition-colors">Dashboard</a>
                @else
                    <a href="{{ route('login') }}"
                        class="px-4 py-2 border border-[#19140035] rounded text-sm hover:border-[#1915014a] transition-colors">Login</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1 flex items-center justify-center py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-2xl mx-auto">
                <!-- Welcome Card -->
                <div class="bg-white border border-[#e3e3e0] rounded-lg shadow-sm p-8">
                    <h1 class="text-2xl font-medium mb-2">Welcome to Foodpanda App</h1>
                    <p class="text-[#706f6c] mb-6">
                        This application uses Single Sign-On (SSO) for secure authentication.
                        Login once to access all connected applications.
                    </p>
                    <div class="flex gap-3">
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="px-5 py-2 bg-[#1b1b18] text-white rounded hover:bg-black transition-colors">Go to
                                Dashboard</a>
                        @else
                            <a href="{{ route('login') }}"
                                class="px-5 py-2 bg-[#1b1b18] text-white rounded hover:bg-black transition-colors">Login
                                with SSO</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-[#FDFDFC] border-t border-[#e3e3e0] py-3">
        <div class="container mx-auto px-4 text-center">
            <p class="text-[#706f6c] text-sm">Foodpanda App - SSO</p>
        </div>
    </footer>
</body>

</html>
