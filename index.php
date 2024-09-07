<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script src="https://unpkg.com/unlazy@0.11.3/dist/unlazy.with-hashing.iife.js" defer init></script>
    <script type="text/javascript">
        window.tailwind.config = {
            darkMode: ['class'],
            theme: {
                extend: {
                    colors: {
                        border: 'hsl(var(--border))',
                        input: 'hsl(var(--input))',
                        ring: 'hsl(var(--ring))',
                        background: 'hsl(var(--background))',
                        foreground: 'hsl(var(--foreground))',
                        primary: {
                            DEFAULT: 'hsl(var(--primary))',
                            foreground: 'hsl(var(--primary-foreground))'
                        },
                        secondary: {
                            DEFAULT: 'hsl(var(--secondary))',
                            foreground: 'hsl(var(--secondary-foreground))'
                        },
                        destructive: {
                            DEFAULT: 'hsl(var(--destructive))',
                            foreground: 'hsl(var(--destructive-foreground))'
                        },
                        muted: {
                            DEFAULT: 'hsl(var(--muted))',
                            foreground: 'hsl(var(--muted-foreground))'
                        },
                        accent: {
                            DEFAULT: 'hsl(var(--accent))',
                            foreground: 'hsl(var(--accent-foreground))'
                        },
                        popover: {
                            DEFAULT: 'hsl(var(--popover))',
                            foreground: 'hsl(var(--popover-foreground))'
                        },
                        card: {
                            DEFAULT: 'hsl(var(--card))',
                            foreground: 'hsl(var(--card-foreground))'
                        },
                    }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer base {
            :root {
                --background: 0 0% 100%;
                --foreground: 240 10% 3.9%;
                --card: 0 0% 100%;
                --card-foreground: 240 10% 3.9%;
                --popover: 0 0% 100%;
                --popover-foreground: 240 10% 3.9%;
                --primary: 240 5.9% 10%;
                --primary-foreground: 0 0% 98%;
                --secondary: 240 4.8% 95.9%;
                --secondary-foreground: 240 5.9% 10%;
                --muted: 240 4.8% 95.9%;
                --muted-foreground: 240 3.8% 46.1%;
                --accent: 240 4.8% 95.9%;
                --accent-foreground: 240 5.9% 10%;
                --destructive: 0 84.2% 60.2%;
                --destructive-foreground: 0 0% 98%;
                --border: 240 5.9% 90%;
                --input: 240 5.9% 90%;
                --ring: 240 5.9% 10%;
                --radius: 0.5rem;
            }
            .dark {
                --background: 240 10% 3.9%;
                --foreground: 0 0% 98%;
                --card: 240 10% 3.9%;
                --card-foreground: 0 0% 98%;
                --popover: 240 10% 3.9%;
                --popover-foreground: 0 0% 98%;
                --primary: 0 0% 98%;
                --primary-foreground: 240 5.9% 10%;
                --secondary: 240 3.7% 15.9%;
                --secondary-foreground: 0 0% 98%;
                --muted: 240 3.7% 15.9%;
                --muted-foreground: 240 5% 64.9%;
                --accent: 240 3.7% 15.9%;
                --accent-foreground: 0 0% 98%;
                --destructive: 0 62.8% 30.6%;
                --destructive-foreground: 0 0% 98%;
                --border: 240 3.7% 15.9%;
                --input: 240 3.7% 15.9%;
                --ring: 240 4.9% 83.9%;
            }
        }
    </style>
    <script>
        // Clear cache and cookies on page load
        window.onload = function() {
            document.cookie.split(";").forEach(function(c) { 
                document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/"); 
            });
            if (window.localStorage) {
                localStorage.clear();
            }
            if (window.sessionStorage) {
                sessionStorage.clear();
            }
        };
    </script>
</head>
<body>
    <!-- Navbar -->
    <header class="bg-primary text-primary-foreground p-4">
        <nav class="flex justify-between items-center max-w-7xl mx-auto">
            <h1 class="text-2xl font-bold">Alibaba Investments</h1>
            <ul class="flex space-x-4">
                <li><a href="#about" class="hover:text-secondary-foreground">About Us</a></li>
                <li><a href="#services" class="hover:text-secondary-foreground">Services</a></li>
                <li><a href="#testimonials" class="hover:text-secondary-foreground">Testimonials</a></li>
                <li><a href="#contact" class="hover:text-secondary-foreground">Contact</a></li>
            </ul>
        </nav>
    </header>

    <div class="flex flex-col items-center justify-center min-h-screen bg-background text-foreground p-4">
        <!-- Hero Section -->
        <div class="max-w-4xl w-full bg-card shadow-lg rounded-lg overflow-hidden p-6 mb-8 text-center">
            <h1 class="text-4xl font-bold mb-4 text-primary">Welcome to Alibaba Investments</h1>
            <p class="text-muted-foreground mb-6">Fulfill your financial ambitions with our exceptional investment opportunities.</p>
            <a href="register.php" class="bg-primary text-primary-foreground px-4 py-2 rounded hover:bg-secondary">Start Your Journey</a>
        </div>

        <!-- Services Section -->
        <section id="services" class="max-w-4xl w-full bg-card shadow-lg rounded-lg overflow-hidden p-6 mb-8">
            <h2 class="text-3xl font-bold mb-4 text-primary text-center">Our Services</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <img src="alibaba-group-710666.jpg" alt="Service 1" class="w-20 h-20 mx-auto mb-4">
                    <h3 class="text-2xl font-semibold">Investment Planning</h3>
                    <p class="text-muted-foreground">Tailored investment plans for your financial goals.</p>
                </div>
                <div class="text-center">
                    <img src="alibaba-group-710666.jpg" alt="Service 2" class="w-20 h-20 mx-auto mb-4">
                    <h3 class="text-2xl font-semibold">Portfolio Management</h3>
                    <p class="text-muted-foreground">Expert management of your investment portfolio.</p>
                </div>
                <div class="text-center">
                    <img src="alibaba-group-710666.jpg" alt="Service 3" class="w-20 h-20 mx-auto mb-4">
                    <h3 class="text-2xl font-semibold">Financial Consulting</h3>
                    <p class="text-muted-foreground">Personalized consulting to optimize your wealth.</p>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section id="testimonials" class="max-w-4xl w-full bg-card shadow-lg rounded-lg overflow-hidden p-6 mb-8">
            <h2 class="text-3xl font-bold mb-4 text-primary text-center">What Our Clients Say</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-4 bg-secondary rounded-lg">
                    <p class="text-muted-foreground">"Alibaba Investments helped me achieve my financial goals with their expert guidance."</p>
                    <h3 class="text-primary-foreground font-bold mt-2">- John Doe</h3>
                </div>
                <div class="p-4 bg-secondary rounded-lg">
                    <p class="text-muted-foreground">"The best investment platform I've ever used. Highly recommend!"</p>
                    <h3 class="text-primary-foreground font-bold mt-2">- Jane Smith</h3>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="max-w-4xl w-full bg-card shadow-lg rounded-lg overflow-hidden p-6 mb-8">
            <h2 class="text-3xl font-bold mb-4 text-primary text-center">Get in Touch</h2>
            <form class="space-y-4">
                <div>
                    <label for="name" class="block text-muted-foreground">Name</label>
                    <input type="text" id="name" class="w-full bg-input border border-muted rounded px-4 py-2" required>
                </div>
                <div>
                    <label for="email" class="block text-muted-foreground">Email</label>
                    <input type="email" id="email" class="w-full bg-input border border-muted rounded px-4 py-2" required>
                </div>
                <div>
                    <label for="message" class="block text-muted-foreground">Message</label>
                    <textarea id="message" class="w-full bg-input border border-muted rounded px-4 py-2" rows="4" required></textarea>
                </div>
                <button type="submit" class="bg-primary text-primary-foreground px-4 py-2 rounded hover:bg-secondary">Send Message</button>
            </form>
        </section>
    </div>

    <!-- Footer -->
    <footer class="bg-primary text-primary-foreground p-4 text-center">
        <p>&copy; 2024 Alibaba Investments. All rights reserved.</p>
    </footer>
</body>
</html>
