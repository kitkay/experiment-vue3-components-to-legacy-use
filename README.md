# Vue 3 Components in a Legacy PHP Application

## Purpose

This project demonstrates how to incrementally introduce **Vue 3 + TypeScript** into an existing **PHP application** without rewriting the entire application.

The main architectural goal is to use the **Strangler Fig pattern**:

```text
Existing PHP application
        │
        ├── Legacy PHP pages
        │
        └── Vue 3 components
                │
                └── Gradually replace legacy UI
```

Instead of converting the entire PHP application into a Vue SPA, individual Vue components are mounted into existing PHP HTML using `data-role` attributes.

For example:

```html
<div data-role="app-header"></div>
<div data-role="summary-card"></div>
<div data-role="order-list"></div>
```

The PHP application remains responsible for:

* Server-side rendering
* Business/application integration
* Providing initial data
* Existing legacy functionality

Vue is responsible for:

* Interactive UI components
* New frontend functionality
* Incrementally replacing legacy UI
* Component-level frontend development

---

# Architecture

The application uses:

* Vue 3
* TypeScript
* Vite
* Tailwind CSS v4
* Vitest
* PHP
* RoadRunner
* Nginx
* Docker

The production architecture is:

```text
                         ┌─────────────────┐
                         │     Browser     │
                         └────────┬────────┘
                                  │
                                  ▼
                         ┌─────────────────┐
                         │      Nginx      │
                         │   Load Balancer │
                         └────────┬────────┘
                                  │
                     ┌────────────┴────────────┐
                     │                         │
                     ▼                         ▼
              ┌─────────────┐           ┌─────────────┐
              │   RR App 1  │           │   RR App 2  │
              │  RoadRunner │           │  RoadRunner │
              └──────┬──────┘           └──────┬──────┘
                     │                         │
                     └────────────┬────────────┘
                                  │
                                  ▼
                            PHP Application
                                  │
                                  ▼
                           Vue Integration
```

Nginx is intentionally used as the **load balancer**.

RoadRunner runs the PHP application.

The PHP application serves the Vue-generated assets and HTML.

---

# Vue Architecture

There are two Vue entry points.

## `main.ts`

`main.ts` is the normal Vue development application.

```text
main.ts
   │
   ▼
App.vue
   │
   ├── AppHeader
   ├── SummaryCard
   └── OrderList
```

This allows normal Vue development with Vite.

## `integration.ts`

`integration.ts` is the production integration entry point used by the PHP application.

```text
integration.ts
   │
   ├── AppHeader.vue
   ├── SummaryCard.vue
   └── OrderList.vue
```

It looks for existing PHP elements:

```html
<div data-role="app-header"></div>
<div data-role="summary-card"></div>
<div data-role="order-list"></div>
```

and mounts the corresponding Vue component.

Example:

```ts
document
  .querySelectorAll<HTMLElement>(`[data-role="${name}"]`)
  .forEach((element) => {
    createApp(config.component, config.props).mount(element)
  })
```

This allows Vue components to coexist with legacy PHP markup.

---

# Data Flow

PHP owns the initial application data.

Example:

```php
$payload = [
    'user' => [
        'id' => 1,
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ],
    'orders' => [
        [
            'id' => 1001,
            'product' => 'Keyboard',
            'quantity' => 2,
            'price' => 99.99,
            'status' => 'paid',
        ],
    ],
];
```

PHP exposes the payload to JavaScript:

```php
<script>
window.__APP_DATA__ = <?= json_encode(
    $payload,
    JSON_HEX_TAG |
    JSON_HEX_AMP |
    JSON_HEX_APOS |
    JSON_HEX_QUOT
) ?>;
</script>
```

Vue reads:

```ts
const payload = window.__APP_DATA__
```

and passes the appropriate data to each component.

```text
PHP
 │
 │ window.__APP_DATA__
 ▼
integration.ts
 │
 ├── user ────────► AppHeader
 │
 └── orders ──────► SummaryCard
                  ► OrderList
```

---

# Why Use This Architecture?

A legacy PHP application often cannot be rewritten in one step.

A complete rewrite introduces significant risk:

* Large deployment risk
* Long migration period
* Large regression surface
* Difficult rollback
* Business logic duplication
* Large team coordination cost

This architecture allows incremental migration.

For example:

```text
Phase 1

PHP
 ├── Legacy Header
 ├── Legacy Summary
 ├── Legacy Orders
 └── Legacy Footer
```

Then:

```text
Phase 2

PHP
 ├── Vue Header
 ├── Legacy Summary
 ├── Legacy Orders
 └── Legacy Footer
```

Then:

```text
Phase 3

PHP
 ├── Vue Header
 ├── Vue Summary
 ├── Vue Orders
 └── Legacy Footer
```

Eventually:

```text
PHP
 └── Vue application
```

without requiring the entire application to be migrated at once.

---

# Installing RoadRunner on Linux

RoadRunner requires PHP and Composer.

## 1. Check PHP

```bash
php -v
```

Recommended:

```text
PHP 8.2+
```

Check Composer:

```bash
composer --version
```

If Composer is not installed, install it according to the official Composer installation instructions.

---

# 2. Install RoadRunner PHP Packages

From the PHP application's directory:

```bash
composer require spiral/roadrunner-http nyholm/psr7
```

Install the RoadRunner CLI:

```bash
composer require spiral/roadrunner-cli --dev
```

---

# 3. Download the RoadRunner Binary

Run:

```bash
vendor/bin/rr get
```

This downloads the RoadRunner binary into the project.

Verify:

```bash
vendor/bin/rr --version
```

You should see the installed RoadRunner version.

You can also check:

```bash
ls -lh rr
```

---

# 4. Create the RoadRunner Configuration

Create:

```text
.rr.yaml
```

Example:

```yaml
version: "3"

server:
  command: "php worker.php"
  relay: "pipes"

http:
  address: "0.0.0.0:8000"

  pool:
    num_workers: 4
    max_jobs: 1000
    max_worker_memory: 128

logs:
  mode: development
  level: debug
  output: stdout
  err_output: stderr
```

---

# 5. Start RoadRunner

Run:

```bash
vendor/bin/rr serve
```

RoadRunner should listen on:

```text
http://localhost:8000
```

---

# 6. RoadRunner Worker

The PHP worker is responsible for receiving HTTP requests from RoadRunner.

Typical structure:

```text
php-app/
├── public/
│   ├── index.php
│   └── assets/
│       ├── vue-app.js
│       └── vue-app.css
│
├── worker.php
├── .rr.yaml
├── composer.json
└── vendor/
```

The worker should also be responsible for serving static files if Nginx is acting purely as a load balancer.

For example:

```text
/assets/vue-app.css
/assets/vue-app.js
```

must ultimately be served by the PHP/RoadRunner application.

Make sure the worker returns the correct MIME types:

```text
.css  → text/css
.js   → application/javascript
.json → application/json
.svg  → image/svg+xml
.png  → image/png
.jpg  → image/jpeg
```

---

# Installing the Vue Application

From the Vue project:

```bash
npm install
```

Run the development server:

```bash
npm run dev
```

The normal Vue application is available through Vite.

---

# Building the Vue Integration

The production integration build uses:

```text
src/integration.ts
```

Run:

```bash
npm run build
```

The Vite configuration outputs:

```text
php-app/public/assets/
├── vue-app.js
└── vue-app.css
```

The PHP page then loads:

```html
<link rel="stylesheet" href="/assets/vue-app.css">
<script type="module" src="/assets/vue-app.js"></script>
```

---

# Tailwind CSS

Tailwind CSS v4 is used through the Vite plugin.

Install:

```bash
npm install tailwindcss @tailwindcss/vite
```

Vite configuration:

```ts
import tailwindcss from '@tailwindcss/vite'

plugins: [
  vue(),
  tailwindcss(),
]
```

The stylesheet contains:

```css
@import "tailwindcss";
```

If explicit source detection is required:

```css
@import "tailwindcss";

@source "./**/*.vue";
@source "./**/*.ts";
```

After building, verify Tailwind generated the expected classes:

```bash
grep -o "bg-white" ../php-app/public/assets/vue-app.css | head
```

---

# Nginx Load Balancer

Nginx sits in front of the RoadRunner instances.

Example:

```nginx
worker_processes auto;

events {
    worker_connections 4096;
}

http {
    include /etc/nginx/mime.types;

    upstream roadrunner {
        server app-rr1:8000;
        server app-rr2:8000;
    }

    server {
        listen 80;
        server_name _;

        location / {
            proxy_pass http://roadrunner;
            proxy_http_version 1.1;

            proxy_set_header Host $host;
            proxy_set_header X-Real-IP $remote_addr;
            proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
            proxy_set_header X-Forwarded-Proto $scheme;
            proxy_set_header Connection "";
        }
    }
}
```

The important part is:

```nginx
upstream roadrunner {
    server app-rr1:8000;
    server app-rr2:8000;
}
```

Nginx distributes requests between both RoadRunner instances.

---

# Verifying Static Assets

Check the CSS:

```bash
curl -I http://localhost/assets/vue-app.css
```

Expected:

```text
HTTP/1.1 200 OK
Content-Type: text/css
```

Check JavaScript:

```bash
curl -I http://localhost/assets/vue-app.js
```

Expected:

```text
HTTP/1.1 200 OK
Content-Type: application/javascript
```

Check the actual CSS content:

```bash
curl -s http://localhost/assets/vue-app.css | grep -o "bg-white" | head
```

---

# Testing

Run Vitest:

```bash
npm run test
```

For watch mode:

```bash
npm run test:watch
```

The Vitest configuration uses:

```ts
environment: 'jsdom'
```

so Vue components can be tested in a browser-like environment.

---

# Recommended Project Structure

```text
project/
│
├── vue-app/
│   ├── src/
│   │   ├── components/
│   │   │   ├── AppHeader.vue
│   │   │   ├── SummaryCard.vue
│   │   │   └── OrderList.vue
│   │   │
│   │   ├── App.vue
│   │   ├── main.ts
│   │   ├── integration.ts
│   │   ├── style.css
│   │   └── types/
│   │       └── types.ts
│   │
│   ├── vite.config.ts
│   ├── vitest.config.ts
│   └── package.json
│
├── php-app/
│   ├── public/
│   │   ├── index.php
│   │   └── assets/
│   │       ├── vue-app.js
│   │       └── vue-app.css
│   │
│   ├── worker.php
│   ├── .rr.yaml
│   ├── composer.json
│   └── vendor/
│
└── nginx/
    └── nginx.conf
```

---

# Development Workflow

## 1. Start Vue development

```bash
npm run dev
```

Use the normal Vue application through `main.ts`.

## 2. Build Vue integration

```bash
npm run build
```

This generates:

```text
php-app/public/assets/vue-app.js
php-app/public/assets/vue-app.css
```

## 3. Start RoadRunner

```bash
vendor/bin/rr serve
```

## 4. Start Nginx

Nginx proxies requests to:

```text
app-rr1
app-rr2
```

## 5. Access the PHP application

```text
http://localhost
```

---

# Important Architectural Rules

### 1. Keep `main.ts` and `integration.ts` separate

`main.ts` is for normal Vue development.

`integration.ts` is for the legacy PHP integration.

Do not make the PHP integration depend on the entire `App.vue` application.

### 2. Use stable integration asset names

The PHP application expects:

```text
/assets/vue-app.js
/assets/vue-app.css
```

This avoids requiring a PHP helper to discover Vite's hashed filenames.

### 3. Nginx is the load balancer

Nginx should not become another application layer.

Its primary responsibility is:

```text
Browser
   ↓
Nginx
   ↓
RoadRunner
```

### 4. Both RoadRunner instances must be equivalent

`app-rr1` and `app-rr2` should run the same application version and have access to the same required application files.

### 5. Keep Vue migration incremental

Do not introduce Vue for the sake of replacing everything immediately.

The purpose of this architecture is to allow:

```text
Legacy PHP
     ↓
Vue component
     ↓
More Vue components
     ↓
Larger Vue application
```

while keeping the existing application operational throughout the migration.
