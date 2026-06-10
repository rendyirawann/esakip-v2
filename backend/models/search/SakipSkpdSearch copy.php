<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SakipSkpd;

/**
 * SakipSkpdSearch represents the model behind the search form of `backend\models\SakipSkpd`.
 */
class SakipSkpdSearch extends SakipSkpd
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refskpd_id', 'refurusan_id', 'refbidang_id'], 'integer'],
            [['kode_skpd', 'nama_skpd', 'kepala_skpd', 'nip_kepala', 'jabatan_kepala', 'pangkat_kepala', 'skpd_isaktif', 'refskpd_unit', 'refskpd_keterangan'], 'safe'],
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
        $query = SakipSkpd::find();

        // add conditions that should always apply here

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
            'refskpd_id' => $this->refskpd_id,
            'refurusan_id' => $this->refurusan_id,
            'refbidang_id' => $this->refbidang_id,
            'refskpd_unit' => $this->refskpd_unit,
            'refskpd_keterangan' => $this->refskpd_keterangan,
        ]);

        $query->andFilterWhere(['like', 'kode_skpd', $this->kode_skpd])
            ->andFilterWhere(['like', 'nama_skpd', $this->nama_skpd])
            ->andFilterWhere(['like', 'kepala_skpd', $this->kepala_skpd])
            ->andFilterWhere(['like', 'nip_kepala', $this->nip_kepala])
            ->andFilterWhere(['like', 'jabatan_kepala', $this->jabatan_kepala])
            ->andFilterWhere(['like', 'pangkat_kepala', $this->pangkat_kepala])
            ->andFilterWhere(['like', 'skpd_isaktif', $this->skpd_isaktif]);

        return $dataProvider;
    }
}
