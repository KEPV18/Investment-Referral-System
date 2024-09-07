<html>
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
  </head>
  <body>
    <div class="flex h-screen">
      <div class="bg-primary w-1/5 p-4">
        <h2 class="text-primary-foreground text-lg font-semibold mb-4">لوحة التحكم</h2>
        <ul>
          <li class="py-2 hover:bg-primary/20 rounded-lg cursor-pointer"><a href="index.php">الرئيسية</a></li>
          <li class="py-2 hover:bg-primary/20 rounded-lg cursor-pointer"><a href="admin_payment_requests.php">طلبات الدفع</a></li>
          <li class="py-2 hover:bg-primary/20 rounded-lg cursor-pointer"><a href="add_investment_plan.php">إضافة خطة استثمارية</a></li>
          <li class="py-2 hover:bg-primary/20 rounded-lg cursor-pointer"><a href="manage_users.php">إدارة المستخدمين</a></li>
          <li class="py-2 hover:bg-primary/20 rounded-lg cursor-pointer"><a href="manage_withdrawals.php">إدارة السحوبات</a></li>
          <li class="py-2 hover:bg-primary/20 rounded-lg cursor-pointer"><a href="user.php">عرض المستخدمين</a></li>
          <li class="py-2 hover:bg-primary/20 rounded-lg cursor-pointer">الإعدادات</li>
        </ul>
      </div>

      <div class="flex-1 bg-background p-4">
        <header class="flex justify-between items-center mb-4">
          <h1 class="text-2xl font-semibold text-primary-foreground">لوحة التحكم</h1>
          <button class="bg-secondary text-secondary-foreground px-4 py-2 rounded-lg">تسجيل الخروج</button>
        </header>

        <div class="bg-card p-4 rounded-lg">
          <h2 class="text-lg font-semibold mb-2">مرحبًا بك في لوحة التحكم!</h2>
          <p class="text-muted-foreground">إدارة جميع الأنشطة من هنا.</p>
        </div>
      </div>
    </div>
  </body>
</html>