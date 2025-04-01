<x-layout>
    <x-slot name="title">{{ isset($panitia->id) ? 'Edit Panitia' : 'Tambah Panitia' }}</x-slot>


    <div class="card">
        <div class="card-body">
            <form action="{{ isset($panitia->id) ? route('panitia.update', $panitia->id) : route('panitia.store') }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf
                @if(isset($panitia->id))
                    @method('PUT')
                @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama</label>
                        <input type="text"
                               name="nama"
                               class="form-control @error('nama') is-invalid @enderror"
                               value="{{ old('nama', $panitia->nama) }}"
                               required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Jabatan</label>
                        <select name="jabatan"
                                class="form-select @error('jabatan') is-invalid @enderror"
                                required>
                            <option value="" disabled selected>Pilih Jabatan</option>
                            <option value="Operator" {{ old('jabatan', $panitia->jabatan) === 'Operator' ? 'selected' : '' }}>Operator</option>
                            <option value="Guru" {{ old('jabatan', $panitia->jabatan) === 'Guru' ? 'selected' : '' }}>Guru</option>
                            <option value="Wakil Kepala Sekolah" {{ old('jabatan', $panitia->jabatan) === 'Wakil Kepala Sekolah' ? 'selected' : '' }}>Wakil Kepala Sekolah</option>
                        </select>
                        @error('jabatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Unit</label>
                        <select name="unit"
                                class="form-select @error('unit') is-invalid @enderror"
                                required>
                            <option value="" disabled selected>Pilih Unit</option>
                            <option value="A" {{ old('unit', $panitia->unit) === 'A' ? 'selected' : '' }}>A</option>
                            <option value="B" {{ old('unit', $panitia->unit) === 'B' ? 'selected' : '' }}>B</option>
                            <option value="C" {{ old('unit', $panitia->unit) === 'C' ? 'selected' : '' }}>C</option>
                        </select>
                        @error('unit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $panitia->email) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">No. HP</label>
                        <input type="text"
                               name="no_hp"
                               class="form-control @error('no_hp') is-invalid @enderror"
                               value="{{ old('no_hp', $panitia->no_hp) }}"
                               required>
                        @error('no_hp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Foto</label>
                        <input type="file"
                               name="foto"
                               class="form-control @error('foto') is-invalid @enderror">
                        @error('foto')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label required">Username</label>
                            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                                   value="{{ old('username', $panitia->username) }}" required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label {{ isset($panitia->id) ? '' : 'required' }}">
                                Password {{ isset($panitia->id) ? '(Kosongkan jika tidak ingin mengubah)' : '' }}
                            </label>
                            <div class="input-group">
                                <input type="password" name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       {{ isset($panitia->id) ? '' : 'required' }}>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i id="passwordIcon" class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-outline-secondary" type="button" id="generatePassword">
                                    <i class="fas fa-random"></i> Generate
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    <div class="col-12">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat"
                                  class="form-control @error('alamat') is-invalid @enderror"
                                  rows="3"
                                  required>{{ old('alamat', $panitia->alamat) }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        {{ isset($panitia->id) ? 'Update' : 'Simpan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    document.getElementById('generatePassword').addEventListener('click', function() {
        // Generate random password
        const length = 8;
        const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        let password = "";
        for (let i = 0; i < length; i++) {
            password += charset.charAt(Math.floor(Math.random() * charset.length));
        }

        // Set the generated password to the input field
        document.querySelector('input[name="password"]').value = password;
    });

    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordField = document.querySelector('input[name="password"]');
        const passwordIcon = document.getElementById('passwordIcon');

        // Toggle between password and text types
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            passwordIcon.classList.remove('fa-eye');
            passwordIcon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            passwordIcon.classList.remove('fa-eye-slash');
            passwordIcon.classList.add('fa-eye');
        }
    });
    </script>
    @endpush

</x-layout>
