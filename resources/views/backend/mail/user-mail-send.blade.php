<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $details['title'] }}</title>
    <meta name="description" content="">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset(setting('site_favicon', 'global')) }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Space+Grotesk:wght@400;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --td-ff-body: "Inter", sans-serif;
            --td-ff-heading: "Space Grotesk", sans-serif;
            --td-text-color: rgba(25, 27, 30, 0.8);
            --td-bg-color: #ffffff;
            font-size: 14px;
        }

        body {
            color: var(--td-text-color);
            font-family: var(--td-ff-body);
            font-size: 14px;
            background-color: var(--td-bg-color);
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: var(--td-ff-heading);
            color: #111827;
            font-weight: 700;
            line-height: 1.2;
        }

        .email-container {
            max-width: 640px;
            padding: 60px 40px 65px;
            background-color: #fff;
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 10px;
            margin: 0 auto;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        .header {
            text-align: center;
            margin-bottom: 16px;
        }

        .header .logo img {
            height: 30px;
        }

        .header .intro-contents h1 {
            margin-top: 16px;
            font-size: 32px;
        }

        .main-contents {
            border-radius: 8px;
            border: 1px solid rgba(0, 0, 0, 0.08);
            padding: 30px;
            background-color: #fafafa;
        }

        p {
            margin-bottom: 15px;
            line-height: 22px;
        }

        .td-primary-btn {
            background: #7445FF;
            border-radius: 12px;
            padding: 0 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 50px;
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            width: 100%;
            margin-top: 20px;
        }

        .body-shapes img {
            opacity: 0.3;
            position: absolute;
            z-index: -1;
        }

        .shape-one {
            top: 20%;
            left: 12px;
        }

        .shape-two {
            right: 0;
            top: 0;
        }

        .shape-three {
            bottom: 0;
            left: 0;
        }

        .shape-four {
            top: 30px;
            left: 0;
        }

        @media (max-width: 480px) {
            .email-container {
                padding: 20px;
            }

            .main-contents {
                padding: 16px;
            }

            .header .intro-contents h1 {
                font-size: 22px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">
                <img src="{{ $details['site_logo'] }}" alt="logo">
            </div>
            <div class="intro-contents">
                <h1>{{ $details['title'] }}</h1>
            </div>
        </div>

        <main>
            <div class="main-contents">
                <div class="features-info">
                    <p class="heading">{{ $details['salutation'] }}</p>
                    <span>{!! $details['email_body'] !!}</span>
                </div>
                @if ($details['button_level'])
                    <a href="{{ $details['button_link'] }}" class="td-primary-btn">{{ $details['button_level'] }}</a>
                @endif
            </div>
        </main>
    </div>
</body>

</html>
