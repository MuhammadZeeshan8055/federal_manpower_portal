@extends('layouts.website')

@section('title', 'Employer Register')

@section('content')
<section class="page-hero page-hero--employers">
    <div class="site-wrap page-hero__inner">
        <span class="section-kicker">For employers</span>
        <h1>Register your company.</h1>
        <p>Create an employer account to post vacancies. An admin will review and approve your registration before jobs can go live.</p>
    </div>
</section>

<section class="page-section">
    <div class="site-wrap contact-layout">
        <div class="contact-details">
            <span class="section-kicker">Why register</span>
            <h2>Hire through Federal Manpower</h2>
            <p>Share genuine manpower needs, submit roles for admin approval, and reach candidates who are ready for overseas work.</p>
            <div class="contact-card">
                <strong>Step 1</strong>
                <span>Register your company details</span>
            </div>
            <div class="contact-card">
                <strong>Step 2</strong>
                <span>Wait for admin approval</span>
            </div>
            <div class="contact-card">
                <strong>Step 3</strong>
                <span>Post jobs — each job is activated after admin review</span>
            </div>
            <p style="margin-top: 18px;">
                Already registered?
                <a href="{{ route('employer.login') }}" class="text-link">Employer login <span>&rarr;</span></a>
            </p>
        </div>

        <form class="contact-form" method="POST" action="{{ route('employer.register') }}">
            @csrf
            <h2>Employer registration</h2>

            <label>
                Company name
                <input type="text" name="company_name" value="{{ old('company_name') }}" required maxlength="160">
                @error('company_name') <span class="ep-field-error">{{ $message }}</span> @enderror
            </label>

            <div class="form-row">
                <label>
                    Contact person
                    <input type="text" name="contact_person" value="{{ old('contact_person') }}" required maxlength="120">
                    @error('contact_person') <span class="ep-field-error">{{ $message }}</span> @enderror
                </label>
                <label>
                    Email address
                    <input type="email" name="email" value="{{ old('email') }}" required>
                    @error('email') <span class="ep-field-error">{{ $message }}</span> @enderror
                </label>
            </div>

            <div class="form-row">
                <label>
                    Phone number
                    <input type="tel" name="phone" value="{{ old('phone') }}" maxlength="40">
                    @error('phone') <span class="ep-field-error">{{ $message }}</span> @enderror
                </label>
                <label>
                    Country
                    <input type="text" name="country" value="{{ old('country') }}" maxlength="120" placeholder="e.g. UAE">
                    @error('country') <span class="ep-field-error">{{ $message }}</span> @enderror
                </label>
            </div>

            <div class="form-row">
                <label>
                    City
                    <input type="text" name="city" value="{{ old('city') }}" maxlength="120">
                    @error('city') <span class="ep-field-error">{{ $message }}</span> @enderror
                </label>
                <label>
                    Address
                    <input type="text" name="address" value="{{ old('address') }}" maxlength="255">
                    @error('address') <span class="ep-field-error">{{ $message }}</span> @enderror
                </label>
            </div>

            <div class="form-row">
                <label>
                    Password
                    <input type="password" name="password" required>
                    @error('password') <span class="ep-field-error">{{ $message }}</span> @enderror
                </label>
                <label>
                    Confirm password
                    <input type="password" name="password_confirmation" required>
                </label>
            </div>

            <button class="btn btn-accent" type="submit">Create employer account <span>&rarr;</span></button>
        </form>
    </div>
</section>
@endsection
