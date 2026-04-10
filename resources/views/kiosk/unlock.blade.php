<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiosk PIN Unlock</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .unlock-card {
            background: white;
            border-radius: 20px;
            padding: 50px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 450px;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .unlock-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .unlock-icon {
            font-size: 48px;
            color: #667eea;
            margin-bottom: 15px;
        }

        .unlock-title {
            font-size: 28px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 10px;
        }

        .unlock-subtitle {
            font-size: 14px;
            color: #6b7280;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
            letter-spacing: 3px;
        }

        .form-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-input::placeholder {
            letter-spacing: normal;
        }

        .submit-btn {
            width: 100%;
            padding: 12px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .back-btn {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            color: #764ba2;
        }

        .error-alert {
            background: #fee;
            border-left: 4px solid #f87171;
            color: #991b1b;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-icon {
            font-size: 18px;
            flex-shrink: 0;
        }

        .pin-dots {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 15px;
        }

        .pin-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #e5e7eb;
            transition: all 0.3s ease;
        }

        .pin-dot.filled {
            background: #667eea;
        }

        .info-box {
            background: #f3f4f6;
            border-left: 4px solid #667eea;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #374151;
            line-height: 1.5;
        }

        @media (max-width: 640px) {
            .unlock-card {
                padding: 30px;
                margin: 20px;
            }

            .unlock-title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="unlock-card">
        <div class="unlock-header">
            <div class="unlock-icon">
                <i class="fas fa-lock"></i>
            </div>
            <h1 class="unlock-title">Unlock Kiosk</h1>
            <p class="unlock-subtitle">Enter the PIN code to access the kiosk</p>
        </div>

        @if ($errors->has('error'))
            <div class="error-alert">
                <i class="fas fa-exclamation-circle error-icon"></i>
                <span>{{ $errors->first('error') }}</span>
            </div>
        @endif

        @if ($errors->has('pin'))
            <div class="error-alert">
                <i class="fas fa-exclamation-circle error-icon"></i>
                <span>{{ $errors->first('pin') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="error-alert">
                <i class="fas fa-exclamation-circle error-icon"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="info-box">
            <i class="fas fa-info-circle" style="margin-right: 8px;"></i>
            Please provide the 4-digit PIN code to unlock the attendance kiosk.
        </div>

        <form action="{{ route('kiosk.verify-pin') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label" for="pin">PIN Code</label>
                <input 
                    type="password" 
                    id="pin" 
                    name="pin" 
                    class="form-input @error('pin') border-red-500 @enderror" 
                    placeholder="••••" 
                    maxlength="4"
                    inputmode="numeric"
                    pattern="[0-9]*"
                    required
                    autocomplete="off"
                >
                <div class="pin-dots" id="pinDots">
                    <div class="pin-dot"></div>
                    <div class="pin-dot"></div>
                    <div class="pin-dot"></div>
                    <div class="pin-dot"></div>
                </div>
                @error('pin')
                    <p style="color: #dc2626; font-size: 12px; margin-top: 5px;">
                        <i class="fas fa-times-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <button type="submit" class="submit-btn">
                <i class="fas fa-unlock mr-2"></i>Unlock Kiosk
            </button>
        </form>

        <a href="{{ route('landing') }}" class="back-btn">
            <i class="fas fa-arrow-left mr-1"></i>Back to Home
        </a>
    </div>

    <script>
        const pinInput = document.getElementById('pin');
        const pinDots = document.querySelectorAll('.pin-dot');

        pinInput.addEventListener('input', function() {
            const pinLength = this.value.length;
            pinDots.forEach((dot, index) => {
                if (index < pinLength) {
                    dot.classList.add('filled');
                } else {
                    dot.classList.remove('filled');
                }
            });
        });

        // Only allow numbers
        pinInput.addEventListener('keypress', function(event) {
            if (!/[0-9]/.test(event.key)) {
                event.preventDefault();
            }
        });

        // Focus on the input field
        pinInput.focus();
    </script>
</body>
</html>
