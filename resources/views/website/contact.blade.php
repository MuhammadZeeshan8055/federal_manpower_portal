@extends('layouts.website')

@section('title', 'Contact Us')

@section('content')
<section class="page-hero"><div class="site-wrap page-hero__inner"><span class="section-kicker">Contact us</span><h1>Talk to Federal Manpower.</h1><p>Ask about candidate applications, overseas vacancies, employer registration, or the status of a case.</p></div></section>
<section class="page-section">
    <div class="site-wrap contact-layout">
        <div class="contact-details">
            <span class="section-kicker">Get in touch</span>
            <h2>Talk to our office staff</h2>
            <p>Reach us during office hours. For a faster start, candidates and employers can also use the online portals.</p>

            <div class="contact-card">
                <strong>Visit our office</strong>
                <span>Office No. 307, Talha Heights, 6th Rd, Satellite Town, Rawalpindi, 46000</span>
            </div>

            <a href="mailto:info@federalmanpower.com" class="contact-card">
                <strong>Email us</strong>
                <span>info@federalmanpower.com</span>
            </a>

            <a href="tel:+92512716031" class="contact-card">
                <strong>Landline</strong>
                <span>+92 51 2716031</span>
            </a>

            <div class="contact-card">
                <strong>Mobile</strong>
                <span>
                    <a href="tel:+923004340666">+92 300 4340666</a><br>
                    <a href="tel:+923024340666">+92 302 4340666</a><br>
                    <a href="tel:+923044340666">+92 304 4340666</a><br>
                    <a href="tel:+923301000069">+92 330 1000069</a>
                </span>
            </div>

            <p style="margin-top:18px;"><a href="{{ route('client-portal.register') }}" class="text-link">Candidate register <span>&rarr;</span></a></p>
            <p><a href="{{ route('employer.register') }}" class="text-link">Employer register <span>&rarr;</span></a></p>
        </div>

        <form class="contact-form" action="mailto:info@federalmanpower.com" method="post" enctype="text/plain">
            <h2>Send an enquiry</h2>
            <div class="form-row">
                <label>Full name<input type="text" name="name" required></label>
                <label>Email address<input type="email" name="email" required></label>
            </div>
            <div class="form-row">
                <label>Phone number<input type="tel" name="phone"></label>
                <label>Enquiry type<select name="type"><option>Candidate support</option><option>Employer recruitment</option><option>Application status</option><option>General enquiry</option></select></label>
            </div>
            <label>Message<textarea name="message" rows="6" required placeholder="Tell us how we can help"></textarea></label>
            <button class="btn btn-accent" type="submit">Send enquiry <span>&rarr;</span></button>
        </form>
    </div>
</section>
@endsection
