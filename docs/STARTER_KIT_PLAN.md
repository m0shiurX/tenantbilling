# Starter Kit Implementation Plan

> **Version:** 1.0  
> **Date:** January 2026  
> **Project:** Space - Laravel Starter Application

## Overview

This document outlines the common features and modules that should be included in this starter application to serve as a foundation for future custom dashboard solutions. The goal is to create a reusable, well-structured base that saves development time on new projects.

---

## Table of Contents

1. [Core Stack](#core-stack)
2. [Common Modules](#common-modules)
3. [Settings Page Architecture](#settings-page-architecture)
4. [Implementation Priority](#implementation-priority)
5. [Recommended Packages](#recommended-packages)
6. [Dark Mode Decision](#dark-mode-decision)
7. [Mobile-First Design Guidelines](#mobile-first-design-guidelines)
8. [Additional Recommendations](#additional-recommendations)

---

## Core Stack

| Layer       | Technology         | Version     |
| ----------- | ------------------ | ----------- |
| Backend     | Laravel            | 12.x        |
| Frontend    | Vue 3 + Inertia v2 | 3.5.x / 2.x |
| Styling     | Tailwind CSS       | 4.x         |
| Routing     | Laravel Wayfinder  | 0.1.x       |
| Auth        | Laravel Fortify    | 1.x         |
| Permissions | Spatie Permission  | 6.x         |
| Testing     | Pest               | 4.x         |

---

## Common Modules

### 1. Authentication (✅ Exists)
- [x] Login / Logout
- [x] Registration
- [x] Password Reset
- [x] Email Verification
- [x] Two-Factor Authentication (TOTP)
- [x] Recovery Codes

### 2. User Profile (✅ Exists - needs grouping)
- [x] Profile Information (name, email, avatar)
- [x] Password Change
- [x] Two-Factor Setup
- [x] Appearance Preferences
- [ ] Session Management (view/revoke active sessions)

### 3. User Management (✅ Implemented)
Backend:
- [x] `User` model enhancements (avatar, status, last_login_at)
- [x] `UserController` with CRUD operations
- [x] `UserStatus` enum with Active/Inactive states
- [x] Form Request validation (StoreUserRequest, UpdateUserRequest)
- [ ] User invitation system (optional)

Frontend:
- [x] Users list page with search/filter
- [x] User create/edit form
- [x] Role assignment UI
- [x] User status toggle (active/inactive)

Routes:
```
GET    /settings/users           → index
GET    /settings/users/create    → create
POST   /settings/users           → store
GET    /settings/users/{user}    → show
GET    /settings/users/{user}/edit → edit
PUT    /settings/users/{user}    → update
DELETE /settings/users/{user}    → destroy
```

### 4. Roles & Permissions (✅ Implemented)
> Uses Spatie Permission (already installed with middleware)

Backend:
- [x] `RoleController` with CRUD
- [x] `PermissionController` (view only, permissions defined in code)
- [x] Permission seeder with grouped permissions (uses `group` field in permissions table)
- [x] Middleware for permission checks (Spatie built-in: `permission:`, `role:`)
- [x] Form Request validation (StoreRoleRequest, UpdateRoleRequest)

Frontend:
- [x] Roles list page with card layout
- [x] Role create/edit with permission checkboxes
- [x] Permissions grouped by `group` field (filterable tabs)
- [x] Permissions index page (read-only)

> **Note:** The `permissions` table has a `group` column that should be used for grouping and filtering permissions in the UI (see reference screenshot).

Permission Naming Convention:
```php
// Format: {resource}_{action}
// Group stored in `group` column
[
    'backup_access', 'backup_create', 'backup_delete', 'backup_download', 'backup_restore', 'backup_settings',  // Group: Backup & Restore
    'user_access', 'user_create', 'user_update', 'user_delete', 'user_impersonate',                           // Group: Users
    'role_access', 'role_create', 'role_update', 'role_delete',                                                 // Group: Roles
    'permission_access',                                                                                         // Group: Permissions
]
```

Routes:
```
GET    /settings/roles           → index
GET    /settings/roles/create    → create
POST   /settings/roles           → store
GET    /settings/roles/{role}/edit → edit
PUT    /settings/roles/{role}    → update
DELETE /settings/roles/{role}    → destroy
GET    /settings/permissions     → index (view only)
```

### 5. Business/Company Settings (🔲 To Implement)
> Using custom `Setting` model (not spatie/laravel-settings)

Backend:
- [ ] `Setting` model (key-value with JSON support)
- [ ] `SettingController`
- [ ] Settings cache layer
- [ ] `settings()` helper function
- [ ] Reference project integration (TBD)

Settings Structure:
```php
[
    'company' => [
        'name', 'email', 'phone', 'address',
        'logo', 'favicon', 'timezone', 'date_format'
    ],
    'invoice' => [
        'prefix', 'next_number', 'footer_text',
        'terms_and_conditions', 'paper_size'
    ],
    'notifications' => [
        'email_enabled', 'sms_enabled', 'push_enabled'
    ],
]
```

Frontend:
- [ ] Company Profile form (with logo upload)
- [ ] Invoice Settings form
- [ ] Notification preferences

Routes:
```
GET    /settings/company         → edit
PUT    /settings/company         → update
GET    /settings/invoice         → edit
PUT    /settings/invoice         → update
```

### 6. Activity Logs (🔲 To Implement)
> Package: `spatie/laravel-activitylog` (✅ Installed)

Backend:
- [x] Install and configure package
- [ ] Log important model events
- [ ] `ActivityLogController` for viewing
- [ ] Retention policy (auto-delete old logs)

Frontend:
- [ ] Activity log list with filters
- [ ] Filter by user, action, model type
- [ ] Date range picker
- [ ] Export to CSV (optional)

Logged Events:
- User login/logout
- CRUD operations on important models
- Settings changes
- Permission/role changes

Routes:
```
GET    /settings/activity-logs   → index
DELETE /settings/activity-logs   → destroy (bulk delete)
```

### 7. Backup & Restore (🔲 To Implement)
> Package: `spatie/laravel-backup` (✅ Installed)

Backend:
- [x] Install and configure package
- [ ] `BackupController` for management
- [ ] Schedule automatic backups
- [ ] Cloud storage support (S3, etc.)

Frontend:
- [ ] Backup list with status
- [ ] Create backup button
- [ ] Download backup
- [ ] Delete old backups
- [ ] Restore option (with confirmation)

Routes:
```
GET    /settings/backups         → index
POST   /settings/backups         → store (create new)
GET    /settings/backups/{backup}/download → download
DELETE /settings/backups/{backup} → destroy
```

### 8. Notifications (🔲 To Implement)
Backend:
- [ ] Database notification driver
- [ ] `NotificationController`
- [ ] Real-time with Laravel Echo (optional)
- [ ] Email/SMS notification settings

Frontend:
- [ ] Notification dropdown in header
- [ ] Notification list page
- [ ] Mark as read/unread
- [ ] Notification preferences

Routes:
```
GET    /notifications            → index
PUT    /notifications/{id}/read  → markAsRead
POST   /notifications/read-all   → markAllAsRead
DELETE /notifications/{id}       → destroy
```

---

## Settings Page Architecture

### URL Structure
```
/settings                    → Settings Dashboard (Business & System only)
/account                     → My Account Dashboard (personal settings)
/account/profile             → Profile editing (exists, move from /settings)
/account/password            → Password change (exists, move from /settings)
/account/two-factor          → 2FA setup (exists, move from /settings)
/account/appearance          → Theme preferences (exists, move from /settings)
/account/sessions            → Session management (new)
/settings/company            → Company profile
/settings/invoice            → Invoice settings
/settings/users              → User management
/settings/roles              → Role management
/settings/permissions        → Permission view
/settings/backups            → Backup management
/settings/activity-logs      → Activity log viewer
/settings/notifications      → Notification settings
```

> **Architecture Change:** Account-related pages (Profile, Password, Two-Factor, Appearance) moved from `/settings/*` to `/account/*`. The Settings page now focuses on business/system configuration only. Users access personal settings via "My Account" link in the header user card.

### Settings Dashboard Layout (Business & System Only)

Desktop:
```
┌─────────────────────────────────────────────────────────────┐
│ Settings                                                     │
│ Manage your business configuration                           │
├─────────────────────────────────────────────────────────────┤
│ ▌ BUSINESS                                                  │
│ ┌──────────────┐ ┌──────────────┐                           │
│ │ 🏢 Company   │ │ 📄 Invoice   │                           │
│ │ Profile      │ │ Settings     │                           │
│ └──────────────┘ └──────────────┘                           │
├─────────────────────────────────────────────────────────────┤
│ ▌ ACCESS CONTROL                                            │
│ ┌──────────────┐ ┌──────────────┐ ┌──────────────┐          │
│ │ 👥 Users     │ │ 🎭 Roles     │ │ 🔑 Permissions│         │
│ └──────────────┘ └──────────────┘ └──────────────┘          │
├─────────────────────────────────────────────────────────────┤
│ ▌ SYSTEM                                                    │
│ ┌──────────────┐ ┌──────────────┐ ┌──────────────┐          │
│ │ 💾 Backups   │ │ 📋 Activity  │ │ 🔔 Notifs    │          │
│ │              │ │    Logs      │ │              │          │
│ └──────────────┘ └──────────────┘ └──────────────┘          │
└─────────────────────────────────────────────────────────────┘
```

### My Account Dashboard Layout (Personal Settings)

Desktop:
```
┌─────────────────────────────────────────────────────────────┐
│ My Account                                                   │
│ Manage your personal settings                                │
├─────────────────────────────────────────────────────────────┤
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ 👤 Super Admin                                          │ │
│ │    superadmin@example.com                               │ │
│ │    Last login: Today at 10:30 AM                        │ │
│ └─────────────────────────────────────────────────────────┘ │
├─────────────────────────────────────────────────────────────┤
│ ┌──────────────┐ ┌──────────────┐ ┌──────────────┐          │
│ │ 👤 Profile   │ │ 🔐 Password  │ │ 🛡️ Two-Factor│          │
│ └──────────────┘ └──────────────┘ └──────────────┘          │
│ ┌──────────────┐ ┌──────────────┐                           │
│ │ 🎨 Appearance│ │ 📱 Sessions  │                           │
│ └──────────────┘ └──────────────┘                           │
└─────────────────────────────────────────────────────────────┘
```

Mobile:
```
┌─────────────────────────────┐
│ ← My Account                │
│   Manage your personal...   │  ← subtitle hidden on desktop
├─────────────────────────────┤
│ ┌─────────────────────────┐ │
│ │ 👤 Super Admin          │ │
│ │    superadmin@...       │ │
│ └─────────────────────────┘ │
├─────────────────────────────┤
│ 👤 Profile              → │ │
│ 🔐 Password             → │ │
│ 🛡️ Two-Factor           → │ │
│ 🎨 Appearance           → │ │
│ 📱 Active Sessions      → │ │
└─────────────────────────────┘
```

---

## Implementation Priority

### Phase 1: Foundation (Week 1-2)
1. ✅ Menu system cleanup (DONE)
2. ✅ Settings dashboard page (DONE)
3. ✅ Create My Account page (`/account`) with personal settings (DONE)
4. ✅ Move profile/password/2FA/appearance pages to `/account/*` (DONE)
5. ✅ Create `PageHeader.vue` component (DONE)
6. ✅ Update Settings dashboard (remove Account section) (DONE)

### Phase 2: Access Control (Week 3-4)
1. ✅ User management CRUD (DONE)
2. ✅ Role management with permission assignment (DONE)
3. ✅ Permission viewer (read-only) (DONE)
4. ✅ Added UI components: Select, Table, AlertDialog, Tabs (DONE)

### Phase 3: Business Features (Week 5-6)
1. 🔲 Company/Business settings
2. 🔲 Invoice settings (if applicable)
3. 🔲 Settings helper function

### Phase 4: System Features (Week 7-8)
1. 🔲 Activity logging integration
2. 🔲 Backup management
3. 🔲 Notification system

### Phase 5: Polish (Week 9-10)
1. 🔲 Session management
2. 🔲 Tests for all modules
3. 🔲 Documentation

---

## Recommended Packages

### Already Installed
- `laravel/fortify` - Authentication
- `spatie/laravel-permission` - Roles & Permissions (with middleware)
- `spatie/laravel-activitylog` - Activity Logging ✅
- `spatie/laravel-backup` - Backup Management ✅
- `spatie/laravel-medialibrary` - Media Library ✅
- `inertiajs/inertia-laravel` - SPA bridge
- `laravel/wayfinder` - TypeScript routes

### Settings Approach
> Using custom `Setting` model instead of `spatie/laravel-settings` for more flexibility.
> Reference project will be added for implementation patterns.

---

## Dark Mode Decision

### Recommendation: Keep but make optional

**Pros of keeping:**
- Already implemented in Tailwind (dark: prefix)
- Users may want it for custom projects
- Low maintenance overhead
- Industry standard feature

**Cons of removing:**
- Reduces CSS bundle size slightly
- Simpler codebase

**Suggested approach:**
1. Keep the dark mode CSS classes
2. Set default theme to "light" in appearance settings
3. Optionally hide theme toggle in settings if not needed
4. Document how to fully remove if a project doesn't need it

```typescript
// In appearance settings
const themeOptions = [
    { value: 'light', label: 'Light' },
    { value: 'dark', label: 'Dark' },
    { value: 'system', label: 'System' },
];
```

---

## Mobile-First Design Guidelines

Based on your reference screenshots, follow these patterns:

### 1. Card Layout
- Desktop: Grid of cards (2-3 columns)
- Mobile: Full-width list items with chevron

### 2. Navigation
- Desktop: Sidebar + content area
- Mobile: Bottom nav or hamburger menu

### 3. Forms
- Desktop: Side labels, multi-column
- Mobile: Stacked labels, full-width inputs

### 4. Lists
- Desktop: Table with columns
- Mobile: Cards with essential info only

### 5. Touch Targets
- Minimum 44x44px for tap targets
- Adequate spacing between interactive elements

### 6. Typography
- Use responsive font sizes
- Readable line lengths (max 65-75 characters)

---

## Additional Recommendations

### 1. Child Page Header Pattern

All child pages (except the main dashboard) should have a consistent header:

```
┌─────────────────────────────────────────────────────────────┐
│ ← Permissions                                               │  ← Back button + Title
│   Manage system access control rules                        │  ← Subtitle (hidden on desktop)
├─────────────────────────────────────────────────────────────┤
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ ℹ️ Read Only                                            │ │  ← Optional info banner
│ │    Can view data but cannot create, edit, or delete.   │ │
│ └─────────────────────────────────────────────────────────┘ │
│ [Content here...]                                           │
└─────────────────────────────────────────────────────────────┘
```

Component: `PageHeader.vue`
```vue
<PageHeader
    title="Permissions"
    subtitle="Manage system access control rules"
    :show-back="true"
    back-href="/settings"
/>
```

### 2. Reusable Components
Create a library of common components:
- `PageHeader.vue` - Back button, title, subtitle (subtitle hidden on md+)
- `SettingsCard.vue` - For settings dashboard
- `SettingsListItem.vue` - Mobile list style
- `DataTable.vue` - Reusable table with search/filter
- `ConfirmModal.vue` - Delete confirmations
- `EmptyState.vue` - When lists are empty
- `InfoBanner.vue` - Read-only notices, warnings

### 2. API Patterns
Standardize response formats:
```php
// Success
return response()->json([
    'message' => 'User created successfully',
    'data' => $user
]);

// Error
return response()->json([
    'message' => 'Validation failed',
    'errors' => $validator->errors()
], 422);
```

### 3. Authorization Patterns
Use policies consistently:
```php
$this->authorize('update', $user);
```

Frontend:
```typescript
// Pass permissions via Inertia shared data
const can = usePage().props.auth.can;
if (can.create_users) { ... }
```

### 4. Common CRUD Layout Pattern

For consistent CRUD pages, use a shared layout approach:

#### Index Page (List)
```
┌─────────────────────────────────────────────────────────────┐
│ ← Users                           [+ Create User]           │
│   Manage team members                                        │
├─────────────────────────────────────────────────────────────┤
│ [Search...] [Filter ▼] [Status ▼]                           │
├─────────────────────────────────────────────────────────────┤
│ │ Avatar │ Name        │ Email           │ Role  │ Actions │ │
│ │   👤   │ John Doe    │ john@mail.com   │ Admin │ ✏️ 🗑️  │ │
│ │   👤   │ Jane Smith  │ jane@mail.com   │ User  │ ✏️ 🗑️  │ │
└─────────────────────────────────────────────────────────────┘
```

Mobile: Card-based list with essential info only.

#### Create/Edit Page
```
┌─────────────────────────────────────────────────────────────┐
│ ← Create User                                               │
│   Add a new team member                                      │
├─────────────────────────────────────────────────────────────┤
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ Name                                                     │ │
│ │ [________________________]                               │ │
│ │                                                          │ │
│ │ Email                                                    │ │
│ │ [________________________]                               │ │
│ │                                                          │ │
│ │ Role                                                     │ │
│ │ [Admin ▼                ]                               │ │
│ └─────────────────────────────────────────────────────────┘ │
├─────────────────────────────────────────────────────────────┤
│                                      [Cancel] [Save User]   │
└─────────────────────────────────────────────────────────────┘
```

#### Suggested CRUD Components
```
resources/js/components/crud/
├── CrudLayout.vue          # Wraps index pages
├── CrudHeader.vue          # Title, subtitle, create button
├── CrudFilters.vue         # Search, filter dropdowns
├── CrudTable.vue           # Desktop table with actions
├── CrudCards.vue           # Mobile card list
├── CrudForm.vue            # Form wrapper with save/cancel
├── CrudFormSection.vue     # Grouped form fields
└── CrudPagination.vue      # Pagination controls
```

#### Usage Example
```vue
<CrudLayout>
    <CrudHeader
        title="Users"
        subtitle="Manage team members"
        :create-href="route('settings.users.create')"
        create-label="Create User"
    />
    <CrudFilters :filters="filters" @filter="handleFilter" />
    
    <!-- Desktop -->
    <CrudTable :columns="columns" :data="users" class="hidden md:block">
        <template #actions="{ row }">
            <ActionButtons :edit="route('settings.users.edit', row)" @delete="confirmDelete(row)" />
        </template>
    </CrudTable>
    
    <!-- Mobile -->
    <CrudCards :data="users" class="md:hidden">
        <template #card="{ item }">
            <UserCard :user="item" />
        </template>
    </CrudCards>
    
    <CrudPagination :links="users.links" />
</CrudLayout>
```

### 5. Testing Strategy
- Feature tests for all CRUD operations
- Unit tests for business logic
- Browser tests for critical flows (login, 2FA)

### 5. Environment-Based Features
```php
// config/features.php
return [
    'multi_tenant' => env('FEATURE_MULTI_TENANT', false),
    'activity_logs' => env('FEATURE_ACTIVITY_LOGS', true),
    'backups' => env('FEATURE_BACKUPS', true),
];
```

### 6. Localization Ready
- Use translation keys for all user-facing text
- Support RTL layouts (if needed)
- Date/time formatting per locale

---

## File Structure Recommendation

```
app/
├── Http/
│   └── Controllers/
│       └── Settings/
│           ├── ProfileController.php
│           ├── PasswordController.php
│           ├── TwoFactorController.php
│           ├── AppearanceController.php
│           ├── CompanyController.php
│           ├── UserController.php
│           ├── RoleController.php
│           ├── PermissionController.php
│           ├── BackupController.php
│           ├── ActivityLogController.php
│           └── NotificationController.php
├── Models/
│   ├── User.php
│   ├── Role.php (extend Spatie)
│   ├── Permission.php (extend Spatie)
│   └── Setting.php
└── Policies/
    ├── UserPolicy.php
    ├── RolePolicy.php
    └── SettingPolicy.php

resources/js/
├── pages/
│   ├── account/                    # Personal settings (My Account)
│   │   ├── Index.vue               # My Account dashboard
│   │   ├── profile/
│   │   │   └── Edit.vue
│   │   ├── password/
│   │   │   └── Edit.vue
│   │   ├── two-factor/
│   │   │   └── Show.vue
│   │   ├── appearance/
│   │   │   └── Edit.vue
│   │   └── sessions/
│   │       └── Index.vue
│   └── settings/                   # Business/System settings
│       ├── Index.vue               # Settings dashboard
│       ├── company/
│       ├── users/
│       │   ├── Index.vue
│       │   ├── Create.vue
│       │   └── Edit.vue
│       ├── roles/
│       │   ├── Index.vue
│       │   ├── Create.vue
│       │   └── Edit.vue
│       ├── permissions/
│       │   └── Index.vue           # Read-only with group tabs
│       ├── backups/
│       ├── activity-logs/
│       └── notifications/
└── components/
    ├── PageHeader.vue              # Back button, title, subtitle
    ├── InfoBanner.vue              # Read-only notices
    ├── crud/                       # CRUD components
    │   ├── CrudLayout.vue
    │   ├── CrudHeader.vue
    │   ├── CrudFilters.vue
    │   ├── CrudTable.vue
    │   ├── CrudCards.vue
    │   └── CrudPagination.vue
    └── settings/
        ├── SettingsCard.vue
        ├── SettingsListItem.vue
        └── SettingsSection.vue
```

---

## Next Steps

1. ✅ **Menu system cleanup** - DONE
2. ✅ **Settings Dashboard page** - DONE (needs restructuring)
3. ✅ **Create My Account page** - Move personal settings here (DONE)
4. ✅ **Create PageHeader component** - Back button, title, subtitle (DONE)
5. ✅ **Update Settings page** - Remove Account section, keep Business/System only (DONE)
6. ✅ **Implement User Management** (DONE)
7. ✅ **Implement Role Management** (DONE)
8. 🔲 **Create CRUD components** - Reusable for all CRUD pages
9. 🔲 **Add remaining modules** based on project needs

---

## Changelog

| Date       | Changes                                                                          |
| ---------- | -------------------------------------------------------------------------------- |
| 2026-01-30 | Implemented Phase 2: User CRUD, Role CRUD, Permissions viewer, new UI components |
| 2026-01-30 | Implemented Phase 1: My Account pages, PageHeader component, Settings refactor   |
| 2026-01-30 | Added My Account architecture, CRUD layout patterns, updated packages            |
| 2026-01-29 | Initial plan created                                                             |

---

*This document should be updated as features are implemented.*
