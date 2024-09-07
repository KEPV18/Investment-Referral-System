<!DOCTYPE html>
<html lang="ar">
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
                    },
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
    <title>إضافة خطة استثمارية</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="bg-background text-primary-foreground min-h-screen flex flex-col items-center justify-center p-4">
        <h1 class="text-5xl font-extrabold mb-8 text-center text-accent">إضافة خطة استثمارية</h1>
        <div class="bg-card w-full md:w-1/3 p-10 rounded-lg shadow-2xl border border-border transition-transform transform hover:scale-105">
            <form action="process_investment_plan.php" method="post" class="flex flex-col space-y-6">
                <label for="name" class="text-sm font-medium text-muted">اسم الخطة:</label>
                <input type="text" id="name" name="name" class="input-field border border-muted rounded-md p-3 focus:ring focus:ring-ring transition duration-200 hover:border-accent" required>

                <label for="min_investment" class="text-sm font-medium text-muted">أقل استثمار (جنيه):</label>
                <input type="number" id="min_investment" name="min_investment" class="input-field border border-muted rounded-md p-3 focus:ring focus:ring-ring transition duration-200 hover:border-accent" step="0.01" required>

                <label for="return_percentage" class="text-sm font-medium text-muted">نسبة العائد (%):</label>
                <input type="number" id="return_percentage" name="return_percentage" class="input-field border border-muted rounded-md p-3 focus:ring focus:ring-ring transition duration-200 hover:border-accent" step="0.01" required>

                <label for="duration" class="text-sm font-medium text-muted">المدة (أيام):</label>
                <input type="number" id="duration" name="duration" class="input-field border border-muted rounded-md p-3 focus:ring focus:ring-ring transition duration-200 hover:border-accent" required>

                <label for="details" class="text-sm font-medium text-muted">التفاصيل:</label>
                <textarea id="details" name="details" class="input-field border border-muted rounded-md p-3 focus:ring focus:ring-ring transition duration-200 hover:border-accent h-32" required></textarea>

                <button type="submit" class="bg-primary text-primary-foreground py-3 rounded-md hover:bg-primary/80 transition duration-200 shadow-md">إضافة الخطة</button>
            </form>
        </div>
        <footer>
            <a href="investment_plans.php" class="btn">العودة إلى الخطط</a>
        </footer>
    </div>
    <a href="index.php" class="back-button">العودة إلى لوحة التحكم</a>
</body>
</html>