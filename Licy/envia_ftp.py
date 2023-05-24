def enviar_archivos_ftp(servidor, usuario, contraseña, archivos):
    try:
        # Conexión al servidor FTP
        ftp = FTP(servidor)
        ftp.login(usuario, contraseña)
        ftp.set_pasv(False)  # bingo :) PARA QUITAR ERROR 1101
        ftp.cwd(dir_folios) #cambia a la carpeta de carga

        # Iterar sobre la lista de archivos
        for archivo in archivos:
            # Abrir el archivo en modo lectura binaria
            with open(archivo, 'rb') as file:
                # Subir el archivo al servidor
                ftp.storbinary(f'STOR {archivo}', file)
            print(f'{archivo} enviado correctamente.')
        # Cerrar la conexión FTP
        ftp.quit()

    except Exception as e:
        text_error=('Error durante la transferencia FTP:', e)
        # *** llamar funcion de envio de errores ***
        Envia_errores(num_folio, text_error)
