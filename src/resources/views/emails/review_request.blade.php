<!-- 取引完了申請メールのBlade -->
@component('mail::message')
# 取引評価が届きました
{{ $transaction->purchase->name}} さんから評価が届きました。

取引チャット画面で取引を完了してください。
@component('mail::button', ['url' => route('transaction.show', ['transaction' => $transaction->id])])
取引チャット画面を開く
@endcomponent

よろしくお願いいたします。
@endcomponent