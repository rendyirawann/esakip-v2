import os

files = [
    r"c:\xampp/htdocs/esakip-v2/backend/controllers/SakipMisiController.php",
    r"c:\xampp/htdocs/esakip-v2/backend/controllers/SakipMisiPController.php",
    r"c:\xampp/htdocs/esakip-v2/backend/controllers/SakipTujuanController.php",
    r"c:\xampp/htdocs/esakip-v2/backend/controllers/SakipTujuanPController.php",
    r"c:\xampp/htdocs/esakip-v2/backend/controllers/SakipSasaranController.php",
    r"c:\xampp/htdocs/esakip-v2/backend/controllers/SakipSasaranPController.php"
]

for file_path in files:
    if not os.path.exists(file_path):
        print(f"File not found: {file_path}")
        continue
    
    with open(file_path, "r", encoding="utf-8") as f:
        content = f.read()
        
    # Patch actionLists($id)
    old_lists_pattern = """    public function actionLists($id)
    {"""
    
    new_lists_replacement = """    public function actionLists($id)
    {
        $periode = \\backend\\models\\SakipPeriode::findOne($id);
        $p5Id = $periode ? $periode->refperiode_5tahun_id : null;"""
        
    if old_lists_pattern in content and "$p5Id = $periode ? $periode->refperiode_5tahun_id" not in content:
        content = content.replace(old_lists_pattern, new_lists_replacement)
        # replace the find where conditions in lists action
        content = content.replace("->where(['refperiode_id' => $id])", "->where(['refperiode_5tahun_id' => $p5Id])")
        
    # Patch actionDuplicate
    if "actionDuplicate(" in content:
        # In actionDuplicate, we search for refperiode_id query on SakipVisi or SakipVisiP
        old_dup_find_pattern_visi = "SakipVisi::find()\n            ->where(['refperiode_id' => $newModel->refperiode_id])\n            ->one();"
        new_dup_find_replacement_visi = "$newPeriode = \\backend\\models\\SakipPeriode::findOne($newModel->refperiode_id);\n        $newP5Id = $newPeriode ? $newPeriode->refperiode_5tahun_id : null;\n        $newVisi = SakipVisi::find()\n            ->where(['refperiode_5tahun_id' => $newP5Id])\n            ->one();"
        
        old_dup_find_pattern_visi_inline = "SakipVisi::find()->where(['refperiode_id' => $newModel->refperiode_id])->one();"
        new_dup_find_replacement_visi_inline = "$newPeriode = \\backend\\models\\SakipPeriode::findOne($newModel->refperiode_id);\n        $newP5Id = $newPeriode ? $newPeriode->refperiode_5tahun_id : null;\n        $newVisi = SakipVisi::find()->where(['refperiode_5tahun_id' => $newP5Id])->one();"

        old_dup_find_pattern_visip = "SakipVisiP::find()\n            ->where(['refperiode_id' => $newModel->refperiode_id])\n            ->one();"
        new_dup_find_replacement_visip = "$newPeriode = \\backend\\models\\SakipPeriode::findOne($newModel->refperiode_id);\n        $newP5Id = $newPeriode ? $newPeriode->refperiode_5tahun_id : null;\n        $newVisi = SakipVisiP::find()\n            ->where(['refperiode_5tahun_id' => $newP5Id])\n            ->one();"
        
        old_dup_find_pattern_visip_inline = "SakipVisiP::find()->where(['refperiode_id' => $newModel->refperiode_id])->one();"
        new_dup_find_replacement_visip_inline = "$newPeriode = \\backend\\models\\SakipPeriode::findOne($newModel->refperiode_id);\n        $newP5Id = $newPeriode ? $newPeriode->refperiode_5tahun_id : null;\n        $newVisi = SakipVisiP::find()->where(['refperiode_5tahun_id' => $newP5Id])->one();"

        content = content.replace(old_dup_find_pattern_visi, new_dup_find_replacement_visi)
        content = content.replace(old_dup_find_pattern_visi_inline, new_dup_find_replacement_visi_inline)
        content = content.replace(old_dup_find_pattern_visip, new_dup_find_replacement_visip)
        content = content.replace(old_dup_find_pattern_visip_inline, new_dup_find_replacement_visip_inline)
        
    with open(file_path, "w", encoding="utf-8") as f:
        f.write(content)
    print(f"Patched: {file_path}")
