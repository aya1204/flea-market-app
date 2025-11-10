<!-- 取引完了申請メールのBlade -->
<p>{{ $transaction->purchase->name }} さんから評価が届きました。</p>
<p>取引を完了してください。</p>
<p><a href="{{ route('transaction.show', ['transaction' => $transaction->id]) }}">取引完了画面を開く</a></p>