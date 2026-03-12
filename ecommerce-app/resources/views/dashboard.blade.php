<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Ecommerce App</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#FDFDFC] text-[#1b1b18] min-h-screen flex flex-col">
    <!-- Navigation -->
    <nav class="bg-[#FDFDFC] border-b border-[#e3e3e0] py-3">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <a href="/" class="font-medium text-lg">Ecommerce App</a>
            <div class="flex items-center gap-3">
                <span class="text-[#1b1b18]">Welcome, {{ Auth::user()->name }}</span>
                <a href="{{ route('logout') }}"
                    class="px-4 py-2 border border-[#19140035] rounded text-sm hover:border-[#1915014a] transition-colors">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1 flex items-center justify-center py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <!-- Welcome Card with Green Check -->
                <div class="bg-white border border-[#e3e3e0] rounded-lg shadow-sm p-8 mb-6 relative overflow-hidden">
                    <div class="flex">
                        <div class="flex-1">
                            <h1 class="text-2xl font-medium mb-2">Welcome to your Dashboard</h1>
                            <p class="text-[#706f6c] mb-6">You are now logged in via Single Sign-On (SSO).</p>

                            <div class="mb-6">
                                <h2 class="font-medium mb-3">Your Profile</h2>
                                <div class="space-y-2 text-sm">
                                    <p><span class="text-[#706f6c] w-20 inline-block">Name:</span>
                                        {{ Auth::user()->name }}</p>
                                    <p><span class="text-[#706f6c] w-20 inline-block">Email:</span>
                                        {{ Auth::user()->email }}</p>
                                </div>
                            </div>

                            <div>
                                <h2 class="font-medium mb-3">Quick Actions</h2>
                                <div class="flex gap-3">
                                    <a href="{{ route('logout') }}"
                                        class="px-5 py-2 bg-[#1b1b18] text-white rounded hover:bg-black transition-colors">Logout</a>
                                    <a href="/"
                                        class="px-5 py-2 border border-[#19140035] rounded hover:border-[#1915014a] transition-colors">Go
                                        to Home</a>
                                </div>
                            </div>
                        </div>

                        <!-- Green Circle Check on Right -->
                        <div class="hidden lg:flex items-center justify-center w-40">
                            <div
                                class="w-24 h-24 rounded-full bg-[#f0fdf4] border-2 border-[#16a34a] flex items-center justify-center">
                                <svg class="w-12 h-12 text-[#16a34a]" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feature Cards -->
                <div class="grid md:grid-cols-3 gap-4">
                    <div class="bg-white border border-[#e3e3e0] rounded-lg shadow-sm p-4">
                        <div
                            class="w-10 h-10 rounded-full bg-[#FDFDFC] border border-[#e3e3e0] mb-3 flex items-center justify-center">
                            <div class="w-3 h-3 rounded-full bg-[#dbdbd7]"></div>
                        </div>
                        <h3 class="font-medium mb-2">Secure Authentication</h3>
                        <p class="text-[#706f6c] text-sm">Your account is protected with OAuth 2.0 authentication via
                            the SSO server.</p>
                    </div>
                    <div class="bg-white border border-[#e3e3e0] rounded-lg shadow-sm p-4">
                        <div
                            class="w-10 h-10 rounded-full bg-[#FDFDFC] border border-[#e3e3e0] mb-3 flex items-center justify-center">
                            <div class="w-3 h-3 rounded-full bg-[#dbdbd7]"></div>
                        </div>
                        <h3 class="font-medium mb-2">Single Sign-On</h3>
                        <p class="text-[#706f6c] text-sm">Login once and access multiple applications without
                            re-entering credentials.</p>
                    </div>
                    <div class="bg-white border border-[#e3e3e0] rounded-lg shadow-sm p-4">
                        <div
                            class="w-10 h-10 rounded-full bg-[#FDFDFC] border border-[#e3e3e0] mb-3 flex items-center justify-center">
                            <div class="w-3 h-3 rounded-full bg-[#dbdbd7]"></div>
                        </div>
                        <h3 class="font-medium mb-2">Session Management</h3>
                        <p class="text-[#706f6c] text-sm">Your session is securely managed and can be terminated from
                            any connected app.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-[#FDFDFC] border-t border-[#e3e3e0] py-3">
        <div class="container mx-auto px-4 text-center">
            <p class="text-[#706f6c] text-sm">Ecommerce App - SSO</p>
        </div>
    </footer>
</body>

</html>
