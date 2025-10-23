(function () {
  // DOM取得（DOM＝ブラウザ上の要素ツリー）
	const input = document.getElementById('avatar');
	const preview = document.getElementById('avatarPreview');
	const error = document.getElementById('avatarError');

	if (!input || !preview) return;

  const MAX_MB = 2; // 2MB制限（上限）
	const ALLOW_MIME = ['image/jpeg', 'image/png'];

	input.addEventListener('change', function () {
    error.style.display = 'none';
    error.textContent = '';

    const file = this.files && this.files[0];
    if (!file) return;

    // 種類チェック（MIMEタイプ＝ファイルの種類を示すラベル）
    if (!ALLOW_MIME.includes(file.type)) {
		error.textContent = 'jpeg または png 形式の画像を選択してください。';
		error.style.display = 'block';
    	input.value = ''; // リセット
		return;
    }

    // サイズチェック
    const sizeMB = file.size / 1024 / 1024;
    if (sizeMB > MAX_MB) {
		error.textContent = `画像サイズは ${MAX_MB}MB 以下にしてください。`;
		error.style.display = 'block';
		input.value = '';
		return;
    }

    // 画像プレビュー（URL.createObjectURL＝一時URLを作る関数）
    const blobUrl = URL.createObjectURL(file);
    preview.src = blobUrl;

    // ページ離脱時にURL破棄（メモリ解放）
    preview.onload = () => URL.revokeObjectURL(blobUrl);
	});
})();
