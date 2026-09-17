# TODO - Student Profile UI Fixes

## Step 1: Edit Profile View (`resources/views/student/profile/index.blade.php`)
- [x] 1a. Fix outer grid container: remove `max-width:860px`, change `align-items:start` to `align-items:stretch`, add `width:100%`
- [x] 1b. Make Account Settings card stretch full height
- [x] 1c. Replace Full Name editable input with non-editable text display
- [x] 1d. Replace Email Address editable input with non-editable text display

## Step 2: Update Profile Controller (`app/Http/Controllers/Student/ProfileController.php`)
- [x] 2a. Remove name/email validation and data updates, only handle password

## Step 3: Test
- [x] Verify page fills width with no extra space
- [x] Verify cards stretch to fill height
- [x] Verify Full Name & Email are displayed as text (not editable)
- [x] Verify password change still works

