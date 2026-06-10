<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SakipPenjabatSkpd;

/**
 * SakipPenjabatSkpdSearch represents the model behind the search form of `backend\models\SakipPenjabatSkpd`.
 */
class SakipPenjabatSkpdSearch extends SakipPenjabatSkpd
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refpenjabatskpd_id', 'refskpd_id', 'refperiode_id'], 'integer'],
            [['nama_penjabat', 'nip_penjabat', 'jabatan_eselon', 'pangkat_eselon'], 'safe'],
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
        $query = SakipPenjabatSkpd::find();

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
            'refpenjabatskpd_id' => $this->refpenjabatskpd_id,
            'refskpd_id' => $this->refskpd_id,
            'refperiode_id' => $this->refperiode_id,
        ]);

        $query->andFilterWhere(['like', 'nama_penjabat', $this->nama_penjabat])
            ->andFilterWhere(['like', 'nip_penjabat', $this->nip_penjabat])
            ->andFilterWhere(['like', 'jabatan_eselon', $this->jabatan_eselon])
            ->andFilterWhere(['like', 'pangkat_eselon', $this->pangkat_eselon]);

        return $dataProvider;
    }
}
