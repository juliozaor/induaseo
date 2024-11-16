<!-- resources/views/components/alert_modal.blade.php -->

<div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="background: #FFFFFF; box-shadow: 0px 0px 20px #00000029; border: 1px solid #EDEFF0; border-radius: 10px;">
            <div class="modal-body modal-alerta">
                <!-- Ícono de alerta, se envía dinámicamente -->
                <img id="alertIcon" src="" alt="Alerta Icono" style="width: 73px; height: 73px; margin-bottom: 15px;">
                
                <!-- Texto de la alerta, se envía dinámicamente -->
                <p id="alertText" style="font: normal normal bold 20px/28px 'Neo Sans Std', Arial, sans-serif; color: #000000;text-align: center;"></p>

                   <!-- Línea divisoria -->
                    <div class="divider"></div>
                
                <!-- Botón para cerrar el modal -->
                <button type="button" class="btn btn-submit" data-dismiss="modal" style="background: #000000;color: #FFFFFF;box-shadow: 0px 3px 10px #0000005C;border-radius: 8px;padding: 8px 20px;font: normal normal normal 14px/17px 'Neo Sans Std', Arial, sans-serif;">Aceptar</button>
            </div>
        </div>
    </div>
</div>
