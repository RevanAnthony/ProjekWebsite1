
<?php include 'header.php'; ?>
<section class="py-5">
  <div class="container">
    <h2 class="section-title mb-4">Kontak</h2>
    <?php if (isset($_GET['sent'])): ?>
      <div class="alert alert-success">Pesan terkirim. Kami akan membalas secepatnya.</div>
    <?php endif; ?>
    <div class="row g-4">
      <div class="col-md-6">
        <form method="post" action="process_contact.php" class="card p-4 shadow-sm">
          <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Subjek</label>
            <input type="text" name="subject" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Pesan</label>
            <textarea name="message" class="form-control" rows="5" required></textarea>
          </div>
          <button class="btn btn-danger">Kirim</button>
        </form>
      </div>
      <div class="col-md-6">
        <div class="p-4 bg-light h-100 rounded-4">
          <h5 class="fw-bold">Alamat</h5>
          <p>Jl. Contoh No.123, Kota Anda</p>
          <h5 class="fw-bold mt-3">Jam Operasional</h5>
          <p>Weekdays: 09.00 - 20.00<br>Weekend: 10.00 - 00.00</p>
          <h5 class="fw-bold mt-3">Kontak</h5>
          <p>Telepon: 08xx-xxxx-xxxx<br>Email: goldenspice@gmail.com</p>
        </div>
      </div>
    </div>
  </div>
</section>
<?php include 'footer.php'; ?>
