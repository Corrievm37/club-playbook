<x-app-layout>
    <div class="container mx-auto p-6 max-w-3xl">
        <h1 class="text-2xl font-semibold mb-4">Player Registration @if($club) - {{ $club->name }} @endif</h1>

        <form action="{{ route('registration.store', ['club' => $club->slug]) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Player First Name</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" class="mt-1 w-full border rounded p-2" required />
                    @error('first_name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Player Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" class="mt-1 w-full border rounded p-2" required />
                    @error('last_name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Date of Birth</label>
                    <input type="date" name="dob" value="{{ old('dob') }}" class="mt-1 w-full border rounded p-2" required />
                    @error('dob')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Gender</label>
                    <select name="gender" class="mt-1 w-full border rounded p-2">
                        <option value="">Prefer not to say</option>
                        <option value="male" {{ old('gender')==='male'?'selected':'' }}>Male</option>
                        <option value="female" {{ old('gender')==='female'?'selected':'' }}>Female</option>
                        <option value="other" {{ old('gender')==='other'?'selected':'' }}>Other</option>
                    </select>
                    @error('gender')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Shirt Size</label>
                    <select name="shirt_size" class="mt-1 w-full border rounded p-2">
                        <option value="">Select size</option>
                        @foreach(['XS','S','M','L','XL','XXL'] as $sz)
                            <option value="{{ $sz }}" {{ old('shirt_size')===$sz?'selected':'' }}>{{ $sz }}</option>
                        @endforeach
                    </select>
                    @error('shirt_size')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">South African ID Number</label>
                    <input id="sa_id_number" type="text" name="sa_id_number" value="{{ old('sa_id_number') }}" class="mt-1 w-full border rounded p-2" placeholder="13 digits" inputmode="numeric" autocomplete="off" />
                    <div id="sa_id_error" class="text-red-600 text-sm mt-1 hidden">Invalid South African ID number.</div>
                    @error('sa_id_number')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">School Name</label>
                    <input type="text" name="school_name" value="{{ old('school_name') }}" class="mt-1 w-full border rounded p-2" />
                    @error('school_name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
            </div>

            <h2 class="text-xl font-semibold">Documents</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Player ID Document (PDF or Photo)</label>
                    <input type="file" name="id_document" accept="image/*,application/pdf" capture="environment" class="mt-1 w-full border rounded p-2" />
                    @error('id_document')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Medical Aid Card (PDF or Photo)</label>
                    <input type="file" name="medical_aid_card" accept="image/*,application/pdf" capture="environment" class="mt-1 w-full border rounded p-2" />
                    @error('medical_aid_card')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
            </div>

            <h2 class="text-xl font-semibold">Guardian Details</h2>
            @php
                $u = auth()->user();
                $firstDefault = '';
                $lastDefault = '';
                if ($u && $u->name) {
                    $parts = preg_split('/\s+/', trim($u->name));
                    $firstDefault = $parts[0] ?? '';
                    $lastDefault = trim(implode(' ', array_slice($parts, 1))) ?: '';
                }
                $emailDefault = $u->email ?? '';
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">First Name</label>
                    <input type="text" name="guardian_first_name" value="{{ old('guardian_first_name', $firstDefault) }}" class="mt-1 w-full border rounded p-2" required />
                    @error('guardian_first_name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Last Name</label>
                    <input type="text" name="guardian_last_name" value="{{ old('guardian_last_name', $lastDefault) }}" class="mt-1 w-full border rounded p-2" required />
                    @error('guardian_last_name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Email</label>
                    <input type="email" name="guardian_email" value="{{ old('guardian_email', $emailDefault) }}" class="mt-1 w-full border rounded p-2" />
                    @error('guardian_email')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Phone</label>
                    <input type="text" name="guardian_phone" value="{{ old('guardian_phone') }}" class="mt-1 w-full border rounded p-2" />
                    @error('guardian_phone')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-2">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="consent_guardian" value="1" class="mr-2" required /> I am the parent/guardian and consent to processing this registration.
                </label>
                @error('consent_guardian')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>

            <div class="mt-6">
                <button id="submit_registration" type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Submit Registration</button>
            </div>
        </form>
    </div>
    <script>
        (function(){
            const input = document.getElementById('sa_id_number');
            const error = document.getElementById('sa_id_error');
            const submit = document.getElementById('submit_registration');
            function digitsOnly(s){return (s||'').replace(/\D+/g,'');}
            function luhnCheck(num){
                let sum=0, alt=false;
                for(let i=num.length-1;i>=0;i--){
                    let n=parseInt(num[i],10);
                    if(alt){n*=2;if(n>9)n-=9}
                    sum+=n;alt=!alt;
                }
                return sum%10===0;
            }
            function validate(){
                const v=digitsOnly(input.value);
                const ok=(v.length===13)&&luhnCheck(v);
                if(v.length===0){
                    error.classList.add('hidden');
                    submit.disabled=false;
                    submit.classList.remove('opacity-50','cursor-not-allowed');
                    return;
                }
                if(!ok){
                    error.classList.remove('hidden');
                    submit.disabled=true;
                    submit.classList.add('opacity-50','cursor-not-allowed');
                }else{
                    error.classList.add('hidden');
                    submit.disabled=false;
                    submit.classList.remove('opacity-50','cursor-not-allowed');
                }
            }
            input.addEventListener('input', validate);
            validate();
        })();
    </script>
</x-app-layout>
