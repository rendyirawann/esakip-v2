<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SakipKegiatan;

/**
 * SakipKegiatanSearch represents the model behind the search form of `backend\models\SakipKegiatan`.
 */
class SakipKegiatanSearch extends SakipKegiatan
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refkegiatan_id', 'refurusan_id', 'refbidang_id', 'refprogram_id'], 'integer'],
            [['kode_kegiatan', 'nama_kegiatan', 'kegiatan_isaktif'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = SakipKegiatan::find();

        // add conditions that should always apply here
        $query->with(['urusan', 'bidang', 'program']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'refkegiatan_id' => $this->refkegiatan_id,
            'refurusan_id' => $this->refurusan_id,
            'refbidang_id' => $this->refbidang_id,
            'refprogram_id' => $this->refprogram_id,
        ]);

        $query->andFilterWhere(['like', 'kode_kegiatan', $this->kode_kegiatan])
            ->andFilterWhere(['like', 'nama_kegiatan', $this->nama_kegiatan])
            ->andFilterWhere(['like', 'kegiatan_isaktif', $this->kegiatan_isaktif]);

        return $dataProvider;
    }
}
