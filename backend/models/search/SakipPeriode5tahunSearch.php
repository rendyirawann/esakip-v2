<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SakipPeriode5tahun;

/**
 * SakipPeriode5tahunSearch represents the model behind the search form of `backend\models\SakipPeriode5tahun`.
 */
class SakipPeriode5tahunSearch extends SakipPeriode5tahun
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refperiode_5tahun_id', 'tahun_mulai', 'tahun_selesai'], 'integer'],
            [['nama_periode', 'is_aktif'], 'safe'],
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
        $query = SakipPeriode5tahun::find();

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
            'refperiode_5tahun_id' => $this->refperiode_5tahun_id,
            'tahun_mulai' => $this->tahun_mulai,
            'tahun_selesai' => $this->tahun_selesai,
        ]);

        $query->andFilterWhere(['like', 'nama_periode', $this->nama_periode])
            ->andFilterWhere(['like', 'is_aktif', $this->is_aktif]);

        return $dataProvider;
    }
}
