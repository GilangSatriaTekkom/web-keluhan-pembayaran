<!-- resources/views/components/user-form-fields.blade.php -->
<div class="input-wrapper">
    <div class="form-group">
    <label for="name">Nama</label>
    <input type="text" class="form-control" id="name" name="name" required>
    </div>

    <div class="form-group">
    <label for="email">Email</label>
    <input type="email" class="form-control" id="email" name="email" required>
    </div>

    <div class="form-group">
    <label for="password">Password</label>
    <input type="password" class="form-control" id="password" name="password" required>
    </div>

    <div class="form-group">
    <label for="location">Alamat</label>
    <input type="text" class="form-control" id="location" name="location" required>
    </div>

    <div class="form-group">
    <label for="phone">Nomor Telepon</label>
    <input type="text" class="form-control" id="phone" name="phone" required>
    </div>

    <input type="hidden" name="role" value="{{ $role }}">

</div>
