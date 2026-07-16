# TODO

- [ ] Add EDP lookup to Admin Discipline > Add Record form
  - [x] Add `DisciplineAdminController@lookupEdp` endpoint that searches `students_imports` by `student_id` (EDP)
  - [x] Update `resources/views/admin/discipline/create.blade.php` to allow typing EDP and auto-fill NAME only
  - [x] Add route `admin.discipline.lookup-edp`
- [ ] Adjust form submission so `store()` can save discipline record using `student_id` resolved from imports
  - [ ] Update `DisciplineAdminController@store` to accept `edp` + look up `students_imports` and create discipline case



