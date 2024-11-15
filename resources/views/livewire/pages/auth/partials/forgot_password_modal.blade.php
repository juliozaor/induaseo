<!-- resources/views/livewire/pages/auth/partials/forgot_password_modal.blade.php -->


<div class="modal fade" id="forgotPasswordModal" tabindex="-1" role="dialog" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <!-- Encabezado del modal con el título y botón de cerrar -->
            <div class="modal-header">
                <h5 class="modal-title" id="forgotPasswordModalLabel">Recuperar Contraseña</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Rectángulo debajo del título -->
            <div class="modal-header-bar">
                <p class="modal-header-bar-text">
                    Ingrese su usuario y correo electrónico para iniciar con el proceso de recuperación de contraseña.
                </p>
            </div>

            <form id="forgotPasswordForm" method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-row">
                        <!-- Campo de Usuario -->
                        <div class="form-group flex-grow-1">
                            <label for="usuario" class="label">Usuario</label>
                            <input type="text" id="usuario" name="usuario" class="form-control" required>
                            @error('usuario')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Campo de Correo Electrónico -->
                        <div class="form-group flex-grow-1">
                            <label for="email" class="label">Correo Electrónico</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Botón de enviar en la parte inferior derecha -->
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button> --}}
                    <button type="submit" class="btn btn-submit">Enviar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script para manejar el envío del formulario de recuperación -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Configura el token CSRF para solicitudes AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    
        $('#forgotPasswordForm').on('submit', function (e) {
            e.preventDefault();
    
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                success: function (response) {
                    if (response.status === 'success') {
                        $('#forgotPasswordModal').modal('hide');
    
                        // Mostrar modal de alerta con éxito
                        showAlertModal(
                            "{{ asset('assets/images/ok.png') }}", // Ruta del ícono de éxito
                            "Se ha enviado un enlace de recuperación a tu correo." // Mensaje de éxito
                        );
                    } else {
                        // Mostrar modal de alerta con mensaje de error
                        showAlertModal(
                            "{{ asset('assets/images/ok.png') }}", // Ruta del ícono de error
                            response.message // Mensaje de error
                        );
                    }
                },
                error: function () {
                    // Mostrar modal de alerta en caso de error de solicitud
                    showAlertModal(
                        "{{ asset('assets/images/ok.png') }}", // Ruta del ícono de error
                        "Ocurrió un error al procesar tu solicitud." // Mensaje de error
                    );
                }
            });
        });
    });
    </script>
    
