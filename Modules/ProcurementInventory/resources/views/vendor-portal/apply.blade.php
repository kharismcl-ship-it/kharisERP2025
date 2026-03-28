<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Registration — {{ $companyModel->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen py-10 px-4">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <div class="mb-8 text-center">
                <h1 class="text-2xl font-bold text-gray-900">Vendor Registration</h1>
                <p class="mt-1 text-gray-500">Apply to become a supplier for <span class="font-semibold text-gray-700">{{ $companyModel->name }}</span></p>
            </div>

            @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('vendor.apply.store', $companyModel->slug) }}">
                @csrf

                {{-- Company Information --}}
                <h2 class="text-base font-semibold text-gray-700 mb-4 border-b pb-2">Company Information</h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mb-6">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Business Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Trading Name</label>
                        <input type="text" name="trading_name" value="{{ old('trading_name') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Business Type</label>
                        <select name="business_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="">— Select —</option>
                            <option value="sole_proprietor" @selected(old('business_type')==='sole_proprietor')>Sole Proprietor</option>
                            <option value="partnership" @selected(old('business_type')==='partnership')>Partnership</option>
                            <option value="limited_company" @selected(old('business_type')==='limited_company')>Limited Company</option>
                            <option value="ngo" @selected(old('business_type')==='ngo')>NGO</option>
                            <option value="government" @selected(old('business_type')==='government')>Government</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tax / VAT Number</label>
                        <input type="text" name="tax_number" value="{{ old('tax_number') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Years in Business</label>
                        <input type="number" name="years_in_business" value="{{ old('years_in_business') }}" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>

                {{-- Address --}}
                <h2 class="text-base font-semibold text-gray-700 mb-4 border-b pb-2">Address</h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mb-6">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Street Address</label>
                        <textarea name="address" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('address') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                        <input type="text" name="city" value="{{ old('city') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                        <input type="text" name="country" value="{{ old('country', 'Ghana') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>

                {{-- Contact Person --}}
                <h2 class="text-base font-semibold text-gray-700 mb-4 border-b pb-2">Contact Person</h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Name</label>
                        <input type="text" name="contact_person" value="{{ old('contact_person') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Phone</label>
                        <input type="text" name="contact_phone" value="{{ old('contact_phone') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>

                {{-- Banking --}}
                <h2 class="text-base font-semibold text-gray-700 mb-4 border-b pb-2">Banking Details</h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Account Number</label>
                        <input type="text" name="bank_account_number" value="{{ old('bank_account_number') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bank Branch</label>
                        <input type="text" name="bank_branch" value="{{ old('bank_branch') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>

                {{-- Categories --}}
                @if($categories->isNotEmpty())
                <h2 class="text-base font-semibold text-gray-700 mb-4 border-b pb-2">Categories Supplied</h2>
                <div class="grid grid-cols-2 gap-2 mb-6">
                    @foreach($categories as $category)
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="categories_supplied[]" value="{{ $category->name }}"
                            @checked(in_array($category->name, old('categories_supplied', [])))
                            class="rounded border-gray-300 text-blue-600">
                        {{ $category->name }}
                    </label>
                    @endforeach
                </div>
                @endif

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                    Submit Application
                </button>
            </form>
        </div>
    </div>
</body>
</html>