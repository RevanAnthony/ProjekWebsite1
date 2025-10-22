@extends('layouts.app')
@section('title','Kontak — Golden Spice')

@section('content')
<section class="section">
  <div class="gs-container narrow">
    <h1 class="section-title">Kontak</h1>
    <p class="muted">Ada pertanyaan atau ingin kerja sama? Kirim pesanmu di bawah ini.</p>

    <form class="gs-form" action="#" method="post" onsubmit="return false;">
      <div class="row">
        <input class="input" type="text" placeholder="Nama">
        <input class="input" type="email" placeholder="Email">
      </div>
      <textarea class="textarea" rows="6" placeholder="Pesan"></textarea>
      <button class="gs-btn">Kirim Pesan</button>
    </form>
  </div>
</section>
@endsection
