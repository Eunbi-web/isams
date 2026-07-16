<?php
namespace App\Http\Controllers\SuperAdmin;
use App\Http\Controllers\Controller;
use App\Models\{User,Student};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller {
    public function index(Request $r) {
        $users=User::with('student')->when($r->search,fn($q,$s)=>$q->where('name','like',"%$s%")->orWhere('email','like',"%$s%"))->when($r->role,fn($q,$role)=>$q->where('role',$role))->latest()->paginate(20);
        return view('superadmin.users.index',compact('users'));
    }
    public function create() { return view('superadmin.users.create'); }
    public function store(Request $r) {
        $data=$r->validate(['name'=>'required|string|max:200','email'=>'required|email|unique:users_all','password'=>'required|min:8|confirmed','role'=>'required|in:superadmin,admin,officer,student','department'=>'nullable|string|max:200']);
        $data['password']=Hash::make($data['password']); $data['is_active']=true;
        $user=User::create($data);
        if ($data['role']==='student'&&$r->filled('student_id')) {
            $name=explode(' ',$data['name']);
            Student::create(['user_id'=>$user->id,'first_name'=>$name[0],'last_name'=>implode(' ',array_slice($name,1))?:'Unknown','student_id'=>$r->student_id,'course'=>$r->course??'N/A','year_level'=>$r->year_level??'1st Year','gwa'=>$r->gwa??null,'enrollment_type'=>$r->enrollment_type??'Regular','academic_year'=>'2023-2024','semester'=>'2nd','status'=>'Active']);
        }
        return redirect()->route('superadmin.users.index')->with('success','User created and role assigned!');
    }
    public function edit(User $user) { return view('superadmin.users.edit',compact('user')); }
    public function update(Request $r, User $user) {
        $data=$r->validate(['name'=>'required|string|max:200','email'=>'required|email|unique:users_all,email,'.$user->id,'role'=>'required|in:superadmin,admin,officer,student','department'=>'nullable|string|max:200','is_active'=>'nullable']);
        if ($r->filled('password')) { $r->validate(['password'=>'min:8|confirmed']); $data['password']=Hash::make($r->password); }
        $data['is_active']=$r->boolean('is_active',true);
        $user->update($data);
        return redirect()->route('superadmin.users.index')->with('success','User updated!');
    }
    public function toggleActive(User $user) {
        if ($user->role==='superadmin') return back()->with('error','Cannot deactivate superadmin.');
        $user->update(['is_active'=>!$user->is_active]);
        return back()->with('success','Account '.($user->is_active?'activated':'deactivated').'.');
    }
    public function destroy(User $user) {
        if ($user->role==='superadmin') return back()->with('error','Cannot delete superadmin.');
        $user->delete();
        return redirect()->route('superadmin.users.index')->with('success','User deleted.');
    }
}
