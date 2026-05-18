<?php
$clientPublicAddress = $_SERVER['REMOTE_ADDR'];
?>
<?php
// Query parameter names that trigger "text mode" when present without a value
$textModeKeywordSet = ['raw', 'simple', 'text'];

// Request parameters without a value
$emptyRequestParameterList = array_filter($_GET, function($value) { return $value === ''; });

// "text mode" is enabled if any of the dedicated parameters is present without a value
$textMode = !empty(array_intersect($textModeKeywordSet, array_keys($emptyRequestParameterList)));

// Handle text mode (no HTML, just raw text)
if ($textMode) {
    header('Content-Type: text/plain; charset=us-ascii');
    echo $clientPublicAddress;
    exit;
}
?>
<?php
$copyActionLabel = "Copier"; // French
$copySuccessLabel = "Copié !"; // French
$styleResetDelay = 2000; // in milliseconds
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Google Gemini (A.I.), Maxime BOCHON (human)">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adresse IP publique</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            overflow: hidden;
            background-color: black;
            color: white;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; /* Monospace font alternatives */
        }

        .container {
            width: 100%;
            max-width: 100vw;
            text-align: center;
        }

        .responsive-text {
            /* Gemini:
               In monospace fonts, a character's width is roughly 0.6em.
               For a 15-character maximum, 10vw (10% of viewport width) 
               allows the text to fill the screen without wrapping.
               clamp() enforces a safe minimum (2rem) and maximum (9rem) size. */
            font-size: clamp(2rem, 10vw, 9rem);
            white-space: nowrap; /* Forces text to stay on a single line */
            overflow: hidden;
            text-overflow: ellipsis; /* Safety fallback if text exceeds boundaries */
            margin-bottom: 2rem;
            font-weight: bold;
            letter-spacing: -0.02em;
        }

        .copy-btn {
            background-color: #38bdf8; /* blue */
            color: black;
            border: none;
            padding: 14px 28px;
            font-size: 1rem;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            font-family: inherit;
        }

        .copy-btn:hover {
            background-color: #7dd3fc; /* lighter blue */
            transform: scale(1.05);
        }

        .copy-btn:active {
            transform: scale(0.95);
        }
        
        .copy-btn.success {
            background-color: #4ade80; /* green */
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="responsive-text" id="textToCopy"><?php echo htmlspecialchars($clientPublicAddress); ?></div>
        
        <button class="copy-btn" id="copyBtn"><?php echo htmlspecialchars($copyActionLabel); ?></button>
    </div>

    <script>
        const textElement = document.getElementById('textToCopy');
        const copyButton = document.getElementById('copyBtn');

        const styleResetDelay = <?php echo $styleResetDelay; ?>; // in milliseconds

        copyButton.addEventListener('click', async () => {
            const text = textElement.innerText;
            
            try {
                // Copy text to clipboard
                await navigator.clipboard.writeText(text);
                
                // Visual feedback on success
                copyButton.textContent = '<?php echo htmlspecialchars($copySuccessLabel); ?>';
                copyButton.classList.add('success');
                
                // Reset style after short delay
                setTimeout(() => {
                    copyButton.textContent = '<?php echo htmlspecialchars($copyActionLabel); ?>';
                    copyButton.classList.remove('success');
                }, styleResetDelay);
                
            } catch (err) {
                console.error('Copying to clipboard failed: ', err);
                alert('Erreur lors de la copie automatique.');
            }
        });
    </script>

</body>
</html>
