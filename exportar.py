import os

# Carpetas y archivos que me interesan
RUTAS = [
    "routes/web.php",
    "app/Http/Controllers",
    "app/Models",
    "database/migrations",
    "app/Http/Requests",
    "resources/views",
]

IGNORE = {"vendor", "storage", "node_modules", ".git"}

with open("dump.txt", "w", encoding="utf-8") as out:
    for ruta in RUTAS:
        if not os.path.exists(ruta):
            continue
        if os.path.isfile(ruta):
            out.write(f"\n\n===== ARCHIVO: {ruta} =====\n")
            try:
                with open(ruta, "r", encoding="utf-8", errors="ignore") as f:
                    out.write(f.read())
            except:
                pass
        else:
            for root, dirs, files in os.walk(ruta):
                dirs[:] = [d for d in dirs if d not in IGNORE]
                for file in files:
                    if file.endswith(".php") or file.endswith(".blade.php"):
                        fp = os.path.join(root, file)
                        out.write(f"\n\n===== ARCHIVO: {fp} =====\n")
                        try:
                            with open(fp, "r", encoding="utf-8", errors="ignore") as f:
                                out.write(f.read()[:8000])
                        except:
                            pass

print("LISTO -> Se creó dump.txt")
print("Ahora ejecuta: cat dump.txt | xclip -selection clipboard")
print("O simplemente abre dump.txt y me lo pegas aqui")
