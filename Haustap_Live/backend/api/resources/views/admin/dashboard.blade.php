<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HausTap Admin</title>
    <style>
        html, body { height: 100%; margin: 0; }
        .frame { height: 100vh; width: 100vw; border: 0; }
    </style>
</head>
<body>
    <div style="padding:10px; background:#f8fafc; border-bottom:1px solid #e5e7eb;">
        <form id="applicantForm" style="display:flex; gap:8px; align-items:center;">
            <label for="applicantId">Applicant ID</label>
            <input id="applicantId" name="id" placeholder="e.g. 4D3GS1r1cD0CQPWJfezQ" style="padding:6px;" />
            <button type="button" onclick="doAction('approve')">Approve</button>
            <button type="button" onclick="doAction('reject')">Reject</button>
            <button type="button" onclick="doAction('promote')">Promote to Provider</button>
            <span id="result" style="margin-left:12px; color:#111827;"></span>
        </form>
        <hr style="margin:12px 0; border:none; border-top:1px solid #e5e7eb;" />
        <form id="categoryForm" style="display:flex; gap:8px; align-items:center;">
            <label for="categorySlug">Category</label>
            <input id="categorySlug" placeholder="e.g. cleaning" style="padding:6px;" />
            <input id="categoryPrice" type="number" placeholder="Price" style="padding:6px; width:120px;" />
            <input id="categoryUnit" placeholder="Unit" style="padding:6px; width:120px;" />
            <input id="categoryVariants" placeholder='Variants JSON [{"name":"","price":0,"unit":""}]' style="padding:6px; width:360px;" />
            <button type="button" onclick="setCategoryPrice()">Save Price</button>
            <span id="catResult" style="margin-left:12px; color:#111827;"></span>
        </form>
        <button onclick="aggregateProviders()" style="margin-top:8px;">Aggregate Provider Counts</button>
        <span id="aggResult" style="margin-left:12px; color:#111827;"></span>
        <script>
            async function doAction(action){
                const id = document.getElementById('applicantId').value.trim();
                const result = document.getElementById('result');
                if(!id){ result.textContent = 'Enter an applicant ID'; return }
                result.textContent = 'Processing...';
                try{
                    const url = `/api/firebase/applicants/${id}/${action}`;
                    const res = await fetch(url, { method:'POST', headers:{ 'Accept':'application/json' }});
                    const json = await res.json();
                    result.textContent = JSON.stringify(json);
                }catch(e){ result.textContent = 'Error'; }
            }
            async function setCategoryPrice(){
                const slug = document.getElementById('categorySlug').value.trim();
                const price = parseFloat(document.getElementById('categoryPrice').value || '0');
                const unit = document.getElementById('categoryUnit').value.trim();
                const variantsRaw = document.getElementById('categoryVariants').value.trim();
                const catResult = document.getElementById('catResult');
                if(!slug){ catResult.textContent = 'Enter a category slug'; return }
                let variants = [];
                try{ variants = variantsRaw ? JSON.parse(variantsRaw) : [] }catch(e){ variants = [] }
                catResult.textContent = 'Saving...';
                try{
                    const res = await fetch(`/api/firebase/categories/${slug}/price`, {
                        method:'POST', headers:{ 'Accept':'application/json', 'Content-Type':'application/json' },
                        body: JSON.stringify({ price, unit, variants })
                    });
                    const json = await res.json();
                    catResult.textContent = JSON.stringify(json);
                }catch(e){ catResult.textContent = 'Error'; }
            }
            async function aggregateProviders(){
                const aggResult = document.getElementById('aggResult');
                aggResult.textContent = 'Aggregating...';
                try{
                    const res = await fetch('/api/firebase/categories/aggregate/providers');
                    const json = await res.json();
                    aggResult.textContent = JSON.stringify(json);
                }catch(e){ aggResult.textContent = 'Error'; }
            }
        </script>
    </div>
    <iframe class="frame" src="{{ env('LEGACY_ADMIN_URL', 'http://localhost:5001/admin_haustap/admin_haustap/dashboard.php') }}" allow="clipboard-write; fullscreen" title="Admin"></iframe>
</body>
</html>
