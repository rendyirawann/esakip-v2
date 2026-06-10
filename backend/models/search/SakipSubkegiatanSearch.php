<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SakipSubkegiatan;

/**
 * SakipSubkegiatanSearch represents the model behind the search form of `backend\models\SakipSubkegiatan`.
 */
class SakipSubkegiatanSearch extends SakipSubkegiatan
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refsubkegiatan_id', 'refurusan_id', 'refbidang_id', 'refprogram_id', 'refkegiatan_id'], 'integer'],
            [['kode_subkegiatan', 'nama_subkegiatan', 'subkegiatan_isaktif'], 'safe'],
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
        $query = SakipSubkegiatan::find();

        // add conditions that should always apply here
        $query->with(['urusan', 'bidang', 'program', 'kegiatan']);

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
            'refsubkegiatan_id' => $this->refsubkegiatan_id,
            'refurusan_id' => $this->refurusan_id,
            'refbidang_id' => $this->refbidang_id,
            'refprogram_id' => $this->refprogram_id,
            'refkegiatan_id' => $this->refkegiatan_id,
        ]);

        $query->andFilterWhere(['like', 'kode_subkegiatan', $this->kode_subkegiatan])
            ->andFilterWhere(['like', 'nama_subkegiatan', $this->nama_subkegiatan])
            ->andFilterWhere(['like', 'subkegiatan_isaktif', $this->subkegiatan_isaktif]);

        return $dataProvider;
    }
}
